<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use GuzzleHttp\Client;
use RuntimeException;

/**
 * 后台菜单（权限规则）HTTP 客户端
 *
 * 后台可能独立部署，插件安装时不再直接写 xb_admin_rule 表，而是通过 HTTP 调用后台接口下发菜单：
 *   - 地址来源：.env 的 GATEWAY_URL（后台访问地址，如 http://127.0.0.1:39000）
 *   - 鉴权方式：.env 的 GATEWAY_USER / GATEWAY_PASS 以超管账号登录，换取 JWT 后带 Authorization 头调用
 *   - 写入接口：POST {GATEWAY_URL}/xbAdmin/admin/AdminRule/add（后台按「plugin + path」幂等，存在则更新）
 *
 * 对外 API：
 *   Menu::install($menus, 'xbAdmin');  // 安装插件 config/menu.php 声明的菜单（递归下发 children）
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Menu
{
    /**
     * 菜单类型：10目录 / 20菜单 / 30按钮
     * @var array<int,string>
     */
    protected const TYPES = ['10', '20', '30'];

    /**
     * 开关值：10否 / 20是
     * @var array<int,string>
     */
    protected const FLAGS = ['10', '20'];

    /** @var string 后台网关地址（惰性解析后缓存） */
    protected static string $gateway = '';

    /** @var string 后台访问令牌（惰性登录后缓存） */
    protected static string $token = '';

    /** @var Client|null HTTP 客户端 */
    protected static ?Client $client = null;

    /**
     * 安装菜单（递归下发 config/menu.php 声明的菜单树）
     * @param array $menus 菜单数据，支持 children 嵌套
     * @param string $plugin 插件标识，菜单未声明 plugin 时使用
     * @throws RuntimeException
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function install(array $menus, string $plugin = ''): void
    {
        self::save($menus, $plugin, 0);
    }

    /**
     * 逐条下发菜单，children 以当前菜单ID作为 pid 递归处理
     * @param array $menus
     * @param string $plugin
     * @param int $pid 父级菜单ID，0 表示顶级
     * @throws RuntimeException
     * @return void
     */
    protected static function save(array $menus, string $plugin, int $pid): void
    {
        foreach ($menus as $menu) {
            if (!is_array($menu)) {
                continue;
            }
            $children = $menu['children'] ?? [];
            unset($menu['children']);
            $data = self::prepare($menu, $plugin, $pid);
            $result = self::request('POST', '/xbAdmin/admin/AdminRule/add', ['json' => $data]);
            $id = (int) ($result['id'] ?? 0);
            if (is_array($children) && $children) {
                if ($id <= 0) {
                    throw new RuntimeException('安装菜单失败，后台未返回菜单ID：' . $data['path']);
                }
                self::save($children, $plugin, $id);
            }
        }
    }

    /**
     * 整理菜单数据为接口所需结构（与后台 AdminRuleValidate 的 add 场景对齐）
     * @param array $menu
     * @param string $plugin
     * @param int $pid
     * @throws RuntimeException
     * @return array
     */
    protected static function prepare(array $menu, string $plugin, int $pid): array
    {
        $title = trim((string) ($menu['title'] ?? ''));
        if ($title === '') {
            throw new RuntimeException('安装菜单失败，缺少菜单标题');
        }
        $path = trim((string) ($menu['path'] ?? ''));
        if ($path === '') {
            throw new RuntimeException('安装菜单失败，缺少地址路径');
        }
        $type = (string) ($menu['type'] ?? '');
        if (!in_array($type, self::TYPES, true)) {
            throw new RuntimeException('安装菜单失败，菜单类型错误：' . $path);
        }
        $method = $menu['method'] ?? '';
        if (is_array($method)) {
            $method = implode(',', array_map('strval', $method));
        }
        $method = strtoupper(trim((string) $method));
        if ($method === '') {
            $method = 'GET';
        }
        $params = $menu['params'] ?? '';
        if (is_array($params)) {
            $params = $params ? (string) json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
        }
        return [
            'plugin' => trim((string) ($menu['plugin'] ?? '')) ?: $plugin,
            'title' => $title,
            'short_title' => trim((string) ($menu['short_title'] ?? '')),
            'pid' => $pid > 0 ? $pid : (int) ($menu['pid'] ?? 0),
            'path' => $path,
            'method' => $method,
            'type' => $type,
            'icon' => trim((string) ($menu['icon'] ?? '')),
            'params' => (string) $params,
            'is_show' => self::flag('是否显示', $menu['is_show'] ?? ''),
            'state' => self::flag('是否启用', $menu['state'] ?? ''),
            'is_system' => self::flag('系统菜单', $menu['is_system'] ?? ''),
            'is_default' => self::flag('默认菜单', $menu['is_default'] ?? ''),
            'sort' => (int) ($menu['sort'] ?? 0),
        ];
    }

    /**
     * 解析开关值，未声明时按 10（否）处理
     * @param string $label 字段中文名，用于报错
     * @param mixed $value
     * @throws RuntimeException
     * @return string
     */
    protected static function flag(string $label, mixed $value): string
    {
        if (is_array($value)) {
            $value = current($value);
        }
        $value = (string) $value;
        if ($value === '') {
            return '10';
        }
        if (!in_array($value, self::FLAGS, true)) {
            throw new RuntimeException('安装菜单失败，' . $label . '值错误');
        }
        return $value;
    }

    /**
     * 请求后台接口，并统一校验响应结构
     * @param string $method
     * @param string $path 接口路径，如 /xbAdmin/admin/AdminRule/add
     * @param array $options Guzzle 请求选项
     * @param bool $auth 是否携带登录令牌
     * @throws RuntimeException
     * @return array 响应中的 data
     */
    protected static function request(string $method, string $path, array $options = [], bool $auth = true): array
    {
        $url = self::gateway() . $path;
        // 后台按 Accept 决定异常响应格式，不声明 JSON 时业务异常会渲染成 HTML
        $options['headers'] = array_merge([
            'Accept' => 'application/json',
        ], $options['headers'] ?? []);
        if ($auth) {
            $options['headers']['Authorization'] = 'Bearer ' . self::token();
        }
        try {
            $response = self::client()->request($method, $url, $options);
            $body = (string) $response->getBody();
        } catch (\Throwable $e) {
            throw new RuntimeException('请求后台接口失败：' . $url . '，' . $e->getMessage());
        }
        $json = json_decode($body, true);
        if (!is_array($json)) {
            throw new RuntimeException('请求后台接口失败，返回格式错误：' . $url);
        }
        if ((int) ($json['status'] ?? -1) !== 0) {
            throw new RuntimeException('后台接口返回错误：' . ($json['msg'] ?? '未知错误'));
        }
        return (array) ($json['data'] ?? []);
    }

    /**
     * 获取访问令牌，未登录时以超管账号登录换取
     * @throws RuntimeException
     * @return string
     */
    protected static function token(): string
    {
        if (self::$token === '') {
            self::login();
        }
        return self::$token;
    }

    /**
     * 以 .env 中配置的超管账号登录后台，换取 JWT
     * @throws RuntimeException
     * @return void
     */
    protected static function login(): void
    {
        $username = trim((string) Env::get('GATEWAY_USER', ''));
        $password = (string) Env::get('GATEWAY_PASS', '');
        if ($username === '' || $password === '') {
            throw new RuntimeException('请在 .env 中配置 GATEWAY_USER 与 GATEWAY_PASS（后台超管账号）');
        }
        $data = self::request('POST', '/xbAdmin/admin/Publics/login', [
            'json' => ['username' => $username, 'password' => $password],
        ], false);
        $token = (string) ($data['access_token'] ?? '');
        if ($token === '') {
            throw new RuntimeException('登录后台失败，未获取到访问令牌');
        }
        self::$token = $token;
    }

    /**
     * 获取后台网关地址（.env 的 GATEWAY_URL）
     * @throws RuntimeException
     * @return string
     */
    protected static function gateway(): string
    {
        if (self::$gateway === '') {
            $gateway = rtrim(trim((string) Env::get('GATEWAY_URL', '')), '/');
            if ($gateway === '') {
                throw new RuntimeException('请在 .env 中配置 GATEWAY_URL（后台访问地址）');
            }
            self::$gateway = $gateway;
        }
        return self::$gateway;
    }

    /**
     * 获取 HTTP 客户端
     * @throws RuntimeException
     * @return Client
     */
    protected static function client(): Client
    {
        if (self::$client === null) {
            if (!class_exists(Client::class)) {
                throw new RuntimeException('安装菜单失败，缺少依赖：guzzlehttp/guzzle');
            }
            self::$client = new Client([
                'timeout' => 10,
                'http_errors' => false,
            ]);
        }
        return self::$client;
    }
}