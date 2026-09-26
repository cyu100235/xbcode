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
 * 后台配置分组（系统设置选项卡）HTTP 客户端
 *
 * 后台可能独立部署，插件安装时不再直接写配置表，而是把 setting 目录下的分组模板通过 HTTP 上报后台：
 *   - 地址来源：.env 的 GATEWAY_URL（后台访问地址，如 http://127.0.0.1:39000）
 *   - 鉴权方式：.env 的 GATEWAY_USER / GATEWAY_PASS 以超管账号登录，换取 JWT 后带 Authorization 头调用
 *   - 写入接口：POST {GATEWAY_URL}/xbAdmin/admin/Config/report（后台按「plugin + group」幂等，存在则更新）
 *
 * 同时对外提供 setting 模板解析方法，供插件安装器与后台配置模型复用，保证两端口径一致。
 *
 * 对外 API：
 *   Setting::install(['system' => Setting::parse($file)], 'xbAdmin');  // 上报分组模板
 *   Setting::fields($components);                                      // 提取分组中的字段组件
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Setting
{
    /** @var string 后台网关地址（惰性解析后缓存） */
    protected static string $gateway = '';

    /** @var string 后台访问令牌（惰性登录后缓存） */
    protected static string $token = '';

    /** @var Client|null HTTP 客户端 */
    protected static ?Client $client = null;

    /**
     * 上报配置分组模板
     * @param array $groups 分组数据，键为分组标识，值为 Setting::parse() 的返回值
     * @param string $plugin 插件标识
     * @throws RuntimeException
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function install(array $groups, string $plugin = ''): void
    {
        $plugin = trim($plugin);
        if ($plugin === '') {
            throw new RuntimeException('上报配置失败，插件标识错误');
        }
        $groups = array_values(array_filter($groups, function ($group) {
            return is_array($group) && !empty($group['group']) && !empty($group['body']);
        }));
        if (!$groups) {
            return;
        }
        self::request('POST', '/xbAdmin/admin/Config/report', [
            'json' => ['plugin' => $plugin, 'groups' => $groups],
        ]);
    }

    /**
     * 解析单个 setting 模板文件
     * @param string $file 模板文件绝对路径
     * @return array ['group' => 分组标识, 'title' => 分组标题, 'sort' => 排序, 'body' => 字段组件]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function parse(string $file): array
    {
        if (!is_file($file)) {
            return [];
        }
        $components = require $file;
        if (is_object($components) && method_exists($components, 'get')) {
            $components = [$components];
        }
        if (!is_array($components) || !$components) {
            return [];
        }
        $group = basename($file, '.php');
        $result = self::normalize($components);
        return [
            'group' => $group,
            'title' => $result['title'] !== '' ? $result['title'] : $group,
            'sort' => $result['sort'],
            'body' => $result['body'],
        ];
    }

    /**
     * 归一化分组模板
     *
     * 模板顶层只有唯一容器时，用容器标题作分组标题、容器内容作分组字段，
     * 否则整个模板作为一个分组，标题取分组标识。
     * @param array $components
     * @return array ['title' => 标题, 'sort' => 排序, 'body' => 字段组件]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function normalize(array $components): array
    {
        $components = self::toArray($components);
        $first = reset($components);
        $isWrapper = count($components) === 1
            && is_array($first)
            && !empty($first['title'])
            && !empty($first['body'])
            && is_array($first['body']);
        return [
            'title' => $isWrapper ? (string) $first['title'] : '',
            'sort' => is_array($first) ? (int) ($first['sort'] ?? 0) : 0,
            'body' => $isWrapper ? self::toArray($first['body']) : $components,
        ];
    }

    /**
     * 递归收集带 name 的字段组件，兼容 FieldSet 等容器的 body / items 嵌套
     * @param array $components
     * @return array 键为字段名
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function fields(array $components): array
    {
        $fields = [];
        foreach (self::toArray($components) as $component) {
            if (!is_array($component)) {
                continue;
            }
            $name = trim((string) ($component['name'] ?? ''));
            if ($name !== '') {
                $fields[$name] = $component;
                continue;
            }
            foreach (['body', 'items'] as $key) {
                if (!empty($component[$key]) && is_array($component[$key])) {
                    $fields = array_merge($fields, self::fields($component[$key]));
                }
            }
        }
        return $fields;
    }

    /**
     * 组件对象转数组，模板允许直接返回组件对象
     * @param array $components
     * @return array
     */
    protected static function toArray(array $components): array
    {
        foreach ($components as $key => $component) {
            if (is_object($component) && method_exists($component, 'get')) {
                $components[$key] = $component->get();
            }
        }
        return $components;
    }

    /**
     * 请求后台接口，并统一校验响应结构
     * @param string $method
     * @param string $path 接口路径，如 /xbAdmin/admin/Config/report
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
                throw new RuntimeException('上报配置失败，缺少依赖：guzzlehttp/guzzle');
            }
            self::$client = new Client([
                'timeout' => 10,
                'http_errors' => false,
            ]);
        }
        return self::$client;
    }
}