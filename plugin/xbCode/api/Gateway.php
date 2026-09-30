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
use Throwable;
use Webman\Context;
use Webman\Http\Request as HttpRequest;
use Webman\Http\Response;
use Webman\Util;

/**
 * 后台网关统一出口
 *
 * 插件与后台的一切交互都必须经由本类，各业务类（Config / Setting / Dict / Menu / Upload 等）
 * 不再自行维护网关地址、登录令牌、Guzzle 客户端与响应校验，只负责组装接口路径与请求参数。
 *
 * 通信约定：
 *   - 地址来源：.env 的 GATEWAY_URL（后台访问地址，如 http://127.0.0.1:39000）
 *   - 鉴权方式：.env 的 GATEWAY_TOKEN 作为后台访问令牌，
 *     以 Authorization: Bearer {token} 调用后台接口
 *   - 响应结构：{msg, status, data}，status !== 0 视为业务失败
 *   - 同源场景：网关地址指向本进程时改为「进程内派发」到目标控制器，不发 HTTP
 *     （Windows 单进程下同步自调用会占满工作进程导致请求卡死）
 *
 * 对外 API：
 *   Gateway::request('GET', '/xbAdmin/admin/Config/detail', ['query' => [...]]); // 返回 data
 *   Gateway::get('/xbAdmin/api/Admin/index', $query);                          // 返回完整响应信封
 *   Gateway::selfHosted();                                                     // 网关是否就是本应用
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Gateway
{
    /** @var string 后台网关地址（惰性解析后缓存） */
    protected static string $gateway = '';

    /** @var string 后台访问令牌（.env 的 GATEWAY_TOKEN，惰性读取后缓存） */
    protected static string $token = '';

    /** @var Client|null HTTP 客户端 */
    protected static ?Client $client = null;

    /**
     * 发送GET，返回完整响应信封
     * @param string $path 接口路径
     * @param array|null $data 查询参数
     * @param array $headers 附加请求头
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function get(string $path, ?array $data = null, ?array $headers = []): array
    {
        return self::proxy('GET', $path, ['query' => $data ?? [], 'headers' => $headers]);
    }

    /**
     * 发送POST，返回完整响应信封
     * @param string $path
     * @param array $data
     * @param array $headers
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function post(string $path, ?array $data = [], ?array $headers = []): array
    {
        return self::proxy('POST', $path, ['json' => $data, 'headers' => $headers]);
    }

    /**
     * 发送PUT，返回完整响应信封
     * @param string $path
     * @param array $data
     * @param array $headers
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function put(string $path, ?array $data = [], ?array $headers = []): array
    {
        return self::proxy('PUT', $path, ['json' => $data, 'headers' => $headers]);
    }

    /**
     * 发送DELETE，返回完整响应信封
     * @param string $path
     * @param array $data
     * @param array $headers
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function delete(string $path, ?array $data = [], ?array $headers = []): array
    {
        return self::proxy('DELETE', $path, ['query' => $data ?? [], 'headers' => $headers]);
    }

    /**
     * 请求后台接口，并统一校验响应结构
     * @param string $method 请求方法
     * @param string $path 接口路径，如 /xbAdmin/admin/Config/detail
     * @param array $options 请求选项（multipart / json / query / timeout 等）
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
        $json = self::proxy($method, $path, $options, $auth, $context);
        if ((int) ($json['status'] ?? -1) !== 0) {
            throw new RuntimeException($error . '：' . ($json['msg'] ?? '未知错误'));
        }
        return (array) ($json['data'] ?? []);
    }

    /**
     * 判断后台网关是否就是本应用
     *
     * 网关地址指向本机且端口与本应用监听端口一致时，说明后台与插件跑在同一进程里，
     * 此时必须走本地派发，不能发起 HTTP 请求——同步自调用会占满工作进程导致请求卡死
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
     * 请求分发：同源且处于请求上下文时进程内派发，否则发起 HTTP 请求
     * @param string $method
     * @param string $path
     * @param array $options
     * @param bool $auth
     * @param string $context
     * @return array 完整响应信封
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function proxy(
        string $method,
        string $path,
        array $options = [],
        bool $auth = true,
        string $context = '请求后台接口'
    ): array {
        if (!isset($options['multipart']) && self::contextRequest() !== null && self::selfHosted()) {
            return self::dispatch($method, $path, $options);
        }
        return self::sendHttp($method, $path, $options, $auth, $context);
    }

    /**
     * 发起真实的 HTTP 请求
     * @param string $method
     * @param string $path
     * @param array $options
     * @param bool $auth
     * @param string $context
     * @throws RuntimeException
     * @return array 完整响应信封
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function sendHttp(
        string $method,
        string $path,
        array $options = [],
        bool $auth = true,
        string $context = '请求后台接口'
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
        } catch (Throwable $e) {
            throw new RuntimeException($context . '失败：' . $url . '，' . $e->getMessage());
        }
        $json = json_decode($body, true);
        if (!is_array($json)) {
            throw new RuntimeException($context . '失败，返回格式错误：' . $url);
        }
        return $json;
    }

    /**
     * 进程内派发到目标控制器（同源场景）
     *
     * 按 /插件/应用/控制器/方法 解析出控制器类，继承当前登录态后在进程内直接调用，
     * 不发 HTTP、不走中间件；调用期间把请求上下文切换为目标请求，结束后还原。
     * @param string $method
     * @param string $path
     * @param array $options
     * @throws RuntimeException
     * @return array 完整响应信封
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function dispatch(string $method, string $path, array $options = []): array
    {
        // 兼容路径内直接携带查询串的写法（如 /xbAdmin/api/Admin/edit?id=2）
        if (($pos = strpos($path, '?')) !== false) {
            $query = [];
            parse_str(substr($path, $pos + 1), $query);
            $path = substr($path, 0, $pos);
            $options['query'] = array_merge($query, (array) ($options['query'] ?? []));
        }

        [$plugin, $app, $controller, $action] = self::resolveRoute($path);
        $suffix = (string) config("plugin.{$plugin}.app.controller_suffix", 'Controller');
        $controllerPath = implode('\\', array_map(
            static fn (string $segment): string => ucfirst(strtolower($segment)),
            explode('/', $controller)
        ));
        $controllerClass = "plugin\\{$plugin}\\app\\{$app}\\controller\\{$controllerPath}{$suffix}";
        if (!class_exists($controllerClass) || !method_exists($controllerClass, $action)) {
            throw new RuntimeException("服务端派发失败，接口不存在：{$path}");
        }

        $origin = self::contextRequest();
        $request = self::makeRequest($origin, $method, $path, $options, $plugin, $app, $controller, $action);

        Context::set(HttpRequest::class, $request);
        try {
            $response = (new $controllerClass())->{$action}($request);
        } finally {
            Context::set(HttpRequest::class, $origin);
        }

        $body = $response instanceof Response ? $response->rawBody() : (string) $response;
        $json = json_decode($body, true);
        if (!is_array($json)) {
            throw new RuntimeException("服务端派发失败，返回格式错误：{$path}");
        }
        return $json;
    }

    /**
     * 解析接口路径为 [插件, 应用, 控制器, 方法]
     * @param string $path 如 /xbAdmin/admin/Config/update
     * @throws RuntimeException
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function resolveRoute(string $path): array
    {
        $segments = array_values(array_filter(
            explode('/', trim($path, '/')),
            static fn (string $segment): bool => $segment !== ''
        ));
        if (count($segments) < 4) {
            throw new RuntimeException("服务端派发失败，接口路径非法：{$path}");
        }
        foreach ($segments as $segment) {
            if (!preg_match('/^[a-z_][a-z0-9_]*$/i', $segment)) {
                throw new RuntimeException("服务端派发失败，接口路径非法：{$path}");
            }
        }
        $plugin = self::findPlugin(array_shift($segments));
        if ($plugin === '') {
            throw new RuntimeException("服务端派发失败，插件不存在：{$path}");
        }
        $app = strtolower(array_shift($segments));
        $action = array_pop($segments);
        return [$plugin, $app, implode('/', $segments), $action];
    }

    /**
     * 按接口参数构造目标请求对象（继承登录态，供控制器通过 request() 读取）
     * @param HttpRequest $origin 当前请求
     * @param string $method
     * @param string $path
     * @param array $options
     * @param string $plugin
     * @param string $app
     * @param string $controller
     * @param string $action
     * @return HttpRequest
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function makeRequest(
        HttpRequest $origin,
        string $method,
        string $path,
        array $options,
        string $plugin,
        string $app,
        string $controller,
        string $action
    ): HttpRequest {
        $body = '';
        $headers = ['Accept' => 'application/json'];
        if (array_key_exists('json', $options)) {
            $body = (string) json_encode($options['json'], JSON_UNESCAPED_UNICODE);
            $headers['Content-Type'] = 'application/json';
        }
        foreach (($options['headers'] ?? []) as $name => $value) {
            $headers[$name] = $value;
        }

        $uri = '/' . ltrim($path, '/');
        $query = (array) ($options['query'] ?? []);
        if ($query) {
            $uri .= '?' . http_build_query($query);
        }

        // 依据 Workerman 的报文解析规则组装原始请求报文
        $buffer = $method . ' ' . $uri . ' HTTP/1.1' . "\r\n";
        $buffer .= 'Host: ' . ($origin->header('host') ?: '127.0.0.1') . "\r\n";
        foreach ($headers as $name => $value) {
            $buffer .= $name . ': ' . $value . "\r\n";
        }
        $buffer .= 'Content-Length: ' . strlen($body) . "\r\n\r\n" . $body;

        $class = get_class($origin);
        /** @var HttpRequest $request */
        $request = new $class($buffer);
        $request->plugin = $plugin;
        $request->app = $app;
        $request->controller = implode('/', array_map(
            static fn (string $segment): string => ucfirst(strtolower($segment)),
            explode('/', $controller)
        ));
        $request->action = $action;
        // 继承登录态（uid / role_id / username / is_system / user 等挂在 properties 上）
        $request->properties = $origin->properties;
        return $request;
    }

    /**
     * 获取当前请求上下文中的请求对象（非请求上下文返回 null）
     * @return HttpRequest|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function contextRequest(): ?HttpRequest
    {
        $request = Context::get(HttpRequest::class);
        return $request instanceof HttpRequest ? $request : null;
    }

    /**
     * 匹配已安装的插件名（忽略大小写）
     * @param string $name
     * @return string 命中返回真实目录名，未命中返回空串
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function findPlugin(string $name): string
    {
        static $plugins = null;
        if ($plugins === null) {
            $plugins = [];
            foreach (Util::scanDir(base_path() . '/plugin', false) as $dir) {
                $plugins[strtolower($dir)] = $dir;
            }
        }
        return $plugins[strtolower($name)] ?? '';
    }

    /**
     * 获取后台访问令牌（.env 的 GATEWAY_TOKEN）
     * @throws RuntimeException
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function token(): string
    {
        if (self::$token === '') {
            $token = trim((string) Env::get('GATEWAY_TOKEN', ''));
            if ($token === '') {
                throw new RuntimeException('请在 .env 中配置 GATEWAY_TOKEN（后台访问令牌）');
            }
            self::$token = $token;
        }
        return self::$token;
    }

    /**
     * 获取后台网关地址（.env 的 GATEWAY_URL）
     * @throws RuntimeException
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
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