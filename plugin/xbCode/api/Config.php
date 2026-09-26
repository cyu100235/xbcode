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
 * 后台配置项 HTTP 客户端
 *
 * 后台可能独立部署，插件不直接读配置表，而是通过后台接口读写配置值：
 *   - 地址来源：.env 的 GATEWAY_URL（后台访问地址，如 http://127.0.0.1:39000）
 *   - 鉴权方式：.env 的 GATEWAY_USER / GATEWAY_PASS 以超管账号登录，换取 JWT 后带 Authorization 头调用
 *   - 读取接口：GET  {GATEWAY_URL}/xbAdmin/admin/Config/detail
 *   - 写入接口：POST {GATEWAY_URL}/xbAdmin/admin/Config/update
 *
 * 键以「.」分层：插件名.分组名.字段名.更多层级，分组名即插件 setting 目录下的文件名。
 * 分组值会整组缓存，同一次请求内重复读取不再发起 HTTP 请求。
 *
 * 对外 API：
 *   Config::get('xbAdmin.system.web_name');            // 读字段值
 *   Config::get('xbAdmin.system.web_name.0', '默认值'); // 读字段值内部的层级
 *   Config::set('xbAdmin.system.web_name', '积木云');   // 写字段值（key 一致）
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Config
{
    /** @var string 后台网关地址（惰性解析后缓存） */
    protected static string $gateway = '';

    /** @var string 后台访问令牌（惰性登录后缓存） */
    protected static string $token = '';

    /** @var Client|null HTTP 客户端 */
    protected static ?Client $client = null;

    /** @var array<string,array> 分组配置值缓存，键为「插件标识.分组标识」 */
    protected static array $cache = [];

    /**
     * 读取配置项
     * @param string $key 形如 插件名.分组名.字段名.更多层级
     * @param mixed $default 键不存在时返回的默认值
     * @throws RuntimeException
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        [$plugin, $group, $path] = self::parse($key);
        $value = self::data($plugin, $group);
        if (!$path) {
            return $value ?: $default;
        }
        foreach ($path as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }

    /**
     * 写入配置项
     *
     * 字段值整值覆盖写入；键带更多层级时，先读取字段当前值再按层级写入。
     * @param string $key 形如 插件名.分组名.字段名.更多层级
     * @param mixed $value 待写入的值
     * @throws RuntimeException
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function set(string $key, mixed $value): bool
    {
        [$plugin, $group, $path] = self::parse($key);
        if (!$path) {
            throw new RuntimeException('写入配置失败，缺少字段名：' . $key);
        }
        $name = (string) array_shift($path);
        if ($path) {
            $field = self::get(implode('.', [$plugin, $group, $name]));
            $field = is_array($field) ? $field : [];
            self::assign($field, $path, $value);
            $value = $field;
        }
        self::request('POST', '/xbAdmin/admin/Config/update', [
            'json' => ['plugin' => $plugin, 'group' => $group, 'name' => $name, 'value' => $value],
        ]);
        unset(self::$cache[$plugin . '.' . $group]);
        return true;
    }

    /**
     * 按层级写入数组，中间层级不存在或不是数组时重建为空数组
     * @param array $data
     * @param array $path
     * @param mixed $value
     * @return void
     */
    protected static function assign(array &$data, array $path, mixed $value): void
    {
        $segment = (string) array_shift($path);
        if (!$path) {
            $data[$segment] = $value;
            return;
        }
        if (!isset($data[$segment]) || !is_array($data[$segment])) {
            $data[$segment] = [];
        }
        self::assign($data[$segment], $path, $value);
    }

    /**
     * 解析配置键为「插件标识 / 分组标识 / 层级」
     * @param string $key 形如 插件名.分组名.字段名.更多层级
     * @throws RuntimeException
     * @return array [插件标识, 分组标识, 层级数组]
     */
    protected static function parse(string $key): array
    {
        $segments = array_values(array_filter(array_map('trim', explode('.', $key)), function ($segment) {
            return $segment !== '';
        }));
        if (count($segments) < 2) {
            throw new RuntimeException('配置键格式错误，应为：插件名.分组名.字段名：' . $key);
        }
        [$plugin, $group] = $segments;
        if (!preg_match('/^[\w\-]+$/', $plugin)) {
            throw new RuntimeException('配置键错误，插件标识不合法：' . $key);
        }
        if (!preg_match('/^[\w\-]+$/', $group)) {
            throw new RuntimeException('配置键错误，分组标识不合法：' . $key);
        }
        return [$plugin, $group, array_slice($segments, 2)];
    }

    /**
     * 读取分组配置值，同一次请求内缓存
     * @param string $plugin 插件标识
     * @param string $group 分组标识
     * @throws RuntimeException
     * @return array 键为字段名
     */
    protected static function data(string $plugin, string $group): array
    {
        $cacheKey = $plugin . '.' . $group;
        if (!array_key_exists($cacheKey, self::$cache)) {
            self::$cache[$cacheKey] = self::request('GET', '/xbAdmin/admin/Config/detail', [
                'query' => ['plugin' => $plugin, 'group' => $group],
            ]);
        }
        return self::$cache[$cacheKey];
    }

    /**
     * 请求后台接口，并统一校验响应结构
     * @param string $method
     * @param string $path 接口路径，如 /xbAdmin/admin/Config/detail
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
                throw new RuntimeException('读取配置失败，缺少依赖：guzzlehttp/guzzle');
            }
            self::$client = new Client([
                'timeout' => 10,
                'http_errors' => false,
            ]);
        }
        return self::$client;
    }
}