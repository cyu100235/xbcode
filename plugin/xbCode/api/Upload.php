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
use Webman\Http\UploadFile;

/**
 * 附件上传 HTTP 客户端
 *
 * 附件由后台统一存储，插件不直接依赖后台的附件类，而是通过网关上传：
 *   - 地址来源：.env 的 GATEWAY_URL（后台访问地址，如 http://127.0.0.1:39000）
 *   - 鉴权方式：.env 的 GATEWAY_USER / GATEWAY_PASS 以超管账号登录，换取 JWT 后带 Authorization 头调用
 *   - 上传接口：POST {GATEWAY_URL}/xbAdmin/admin/Upload/upload（multipart/form-data）
 *
 * 网关指向本机且端口与本应用一致时（后台与插件同进程部署），直调后台附件接口，
 * 避免请求内同步自调用造成的死锁（Windows 单进程下必然发生）。
 *
 * 对外 API：
 *   Upload::upload('file');                                  // 上传当前请求中的文件
 *   Upload::file('/path/to/a.jpg');                          // 上传服务器本地文件
 *   Upload::file('/path/to/a.jpg', ['uid' => 5]);            // 指定归属用户
 *   Upload::file('/path/to/a.jpg', ['adapter' => 'local']);  // 指定储存引擎
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Upload
{
    /** @var string 后台上传接口路径 */
    protected const UPLOAD_PATH = '/xbAdmin/admin/Upload/upload';

    /** @var string 后台网关地址（惰性解析后缓存） */
    protected static string $gateway = '';

    /** @var string 后台访问令牌（惰性登录后缓存） */
    protected static string $token = '';

    /** @var Client|null HTTP 客户端 */
    protected static ?Client $client = null;

    /**
     * 上传当前请求中的文件
     * @param string $name 表单字段名
     * @param array $params 附加参数，支持 uid（归属用户）与 adapter（储存引擎）
     * @throws RuntimeException
     * @return array 附件信息，含 uri / url / md5 / size / format / adapter 等
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function upload(string $name = 'file', array $params = []): array
    {
        if (self::selfHosted()) {
            return self::localApi($params)->upload($name);
        }
        $file = request()->file($name);
        if (!$file instanceof UploadFile) {
            throw new RuntimeException('上传文件不存在');
        }
        return self::send(
            $file->getPathname(),
            (string) ($file->getUploadName() ?: $file->getFilename()),
            (string) $file->getUploadMimeType(),
            $params
        );
    }

    /**
     * 上传服务器本地文件
     * @param string $path 文件绝对路径
     * @param array $params 附加参数，支持 uid（归属用户）与 adapter（储存引擎）
     * @throws RuntimeException
     * @return array 附件信息，含 uri / url / md5 / size / format / adapter 等
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function file(string $path, array $params = []): array
    {
        if (!is_file($path)) {
            throw new RuntimeException('上传文件不存在：' . $path);
        }
        if (self::selfHosted()) {
            return self::localApi($params)->uploadFilePath($path);
        }
        return self::send($path, basename($path), (string) (mime_content_type($path) ?: ''), $params);
    }

    /**
     * 判断后台网关是否就是本应用
     *
     * 网关地址指向本机且端口与本应用监听端口一致时，说明后台与插件跑在同一进程里，
     * 此时上传必须直调后台附件接口，不能发起 HTTP 请求——同步自调用会占满工作进程导致请求卡死。
     * @return bool
     */
    protected static function selfHosted(): bool
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
     * 获取后台附件接口（网关与本应用同源时使用）
     * @param array $params 附加参数，支持 uid（归属用户）与 adapter（储存引擎）
     * @throws RuntimeException
     * @return object
     */
    protected static function localApi(array $params): object
    {
        $class = 'plugin\\xbAdmin\\api\\UploadApi';
        if (!class_exists($class)) {
            throw new RuntimeException('上传文件失败，未安装 xbAdmin 后台插件');
        }
        $api = $class::make((string) ($params['adapter'] ?? ''));
        return $api->setUid((int) ($params['uid'] ?? 0));
    }

    /**
     * 以 multipart/form-data 上传文件到后台
     * @param string $path 本地文件路径
     * @param string $filename 客户端文件名
     * @param string $mime 文件类型
     * @param array $params 附加参数，支持 uid（归属用户）与 adapter（储存引擎）
     * @throws RuntimeException
     * @return array 响应中的 data
     */
    protected static function send(string $path, string $filename, string $mime, array $params): array
    {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException('读取上传文件失败：' . $path);
        }
        $multipart = [];
        $uid = (int) ($params['uid'] ?? 0);
        if ($uid > 0) {
            $multipart[] = ['name' => 'uid', 'contents' => (string) $uid];
        }
        $adapter = trim((string) ($params['adapter'] ?? ''));
        if ($adapter !== '') {
            $multipart[] = ['name' => 'adapter', 'contents' => $adapter];
        }
        $multipart[] = [
            'name' => 'file',
            'contents' => $handle,
            'filename' => $filename,
            'headers' => ['Content-Type' => $mime !== '' ? $mime : 'application/octet-stream'],
        ];
        try {
            return self::request('POST', self::UPLOAD_PATH, ['multipart' => $multipart]);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    /**
     * 请求后台接口，并统一校验响应结构
     * @param string $method
     * @param string $path 接口路径，如 /xbAdmin/admin/Upload/upload
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
            throw new RuntimeException('上传文件失败：' . $url . '，' . $e->getMessage());
        }
        $json = json_decode($body, true);
        if (!is_array($json)) {
            throw new RuntimeException('上传文件失败，返回格式错误：' . $url);
        }
        if ((int) ($json['status'] ?? -1) !== 0) {
            throw new RuntimeException('上传文件失败：' . ($json['msg'] ?? '未知错误'));
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
                throw new RuntimeException('上传文件失败，缺少依赖：guzzlehttp/guzzle');
            }
            self::$client = new Client([
                'timeout' => 300,
                'http_errors' => false,
            ]);
        }
        return self::$client;
    }
}