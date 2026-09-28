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
 * 后台网关统一出口
 *
 * 插件与后台的一切 HTTP 交互都必须经由本类，各业务类（Config / Setting / Dict / Menu / Upload 等）
 * 不再自行维护网关地址、登录令牌、Guzzle 客户端与响应校验，只负责组装接口路径与请求参数。
 *
 * 通信约定：
 *   - 地址来源：.env 的 GATEWAY_URL（后台访问地址，如 http://127.0.0.1:39000）
 *   - 鉴权方式：.env 的 GATEWAY_USER / GATEWAY_PASS 以超管账号 POST /xbAdmin/admin/Publics/login 换取 JWT，
 *     再以 Authorization: Bearer {token} 调用其它后台接口
 *   - 响应结构：{msg, status, data}，status !== 0 视为业务失败
 *
 * 对外 API：
 *   Gateway::request('GET', '/xbAdmin/admin/Config/detail', ['query' => [...]]);
 *   Gateway::selfHosted();   // 判断网关是否就是本应用（同源时必须走本地实现）
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Gateway
{
    /** @var string 后台登录接口路径 */
    protected const LOGIN_PATH = '/xbAdmin/admin/Publics/login';

    /** @var string 后台网关地址（惰性解析后缓存） */
    protected static string $gateway = '';

    /** @var string 后台访问令牌（惰性登录后缓存） */
    protected static string $token = '';

    /** @var Client|null HTTP 客户端 */
    protected static ?Client $client = null;

    /**
     * 请求后台接口，并统一校验响应结构
     * @param string $method 请求方法
     * @param string $path 接口路径，如 /xbAdmin/admin/Config/detail
     * @param array $options Guzzle 请求选项（multipart / json / query / timeout 等）
     * @param bool $auth 是否携带登录令牌
     * @param string $context 出错消息前缀，如「请求后台接口」「上传文件」
     * @param string $error 业务错误消息前缀，默认「后台接口返回错误」
     * @throws RuntimeException
     * @return array 响应中的 data
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function request(
        string $method,
        string $path,
        array $options = [],
        bool $auth = true,
        string $context = '请求后台接口',
        string $error = '后台接口返回错误'
    ): array {
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
            throw new RuntimeException($context . '失败：' . $url . '，' . $e->getMessage());
        }
        $json = json_decode($body, true);
        if (!is_array($json)) {
            throw new RuntimeException($context . '失败，返回格式错误：' . $url);
        }
        if ((int) ($json['status'] ?? -1) !== 0) {
            throw new RuntimeException($error . '：' . ($json['msg'] ?? '未知错误'));
        }
        return (array) ($json['data'] ?? []);
    }

    /**
     * 判断后台网关是否就是本应用
     *
     * 网关地址指向本机且端口与本应用监听端口一致时，说明后台与插件跑在同一进程里，
     * 此时必须走本地实现，不能发起 HTTP 请求——同步自调用会占满工作进程导致请求卡死
     * （Windows 单进程下必然发生）。
     * @throws RuntimeException
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function selfHosted(): bool
    {
        $parts = parse_url(self::gateway());
        $host = strtolower((string) ($parts['host'] ?? ''));
        if (!in_array($host, ['127.0.0.1', 'localhost', '::1'], true)) {
            return false;
        }
        // 未显式指定端口时也按同源处理（本机地址）
        if (!isset($parts['port'])) {
            return true;
        }
        return (int) $parts['port'] === (int) Nginx::get('port', 39000);
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
        $data = self::request('POST', self::LOGIN_PATH, [
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
                throw new RuntimeException('请求后台接口失败，缺少依赖：guzzlehttp/guzzle');
            }
            self::$client = new Client([
                'timeout' => 10,
                'http_errors' => false,
            ]);
        }
        return self::$client;
    }
}