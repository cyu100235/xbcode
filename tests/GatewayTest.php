<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use plugin\xbCode\api\Env;
use plugin\xbCode\api\Gateway;
use plugin\xbCode\api\Nginx;
use Psr\Http\Message\RequestInterface;
use ReflectionProperty;
use RuntimeException;

/**
 * Gateway 后台网关出口单元测试
 *
 * 覆盖三件事：请求组装与响应校验、登录换令牌流程、同源判断。
 * HTTP 层用 Guzzle MockHandler 顶掉，不发真实请求，也不依赖 .env 与后台服务。
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class GatewayTest extends TestCase
{
    /** @var array 已发送的请求历史，由 Guzzle history 中间件写入 */
    protected array $history = [];

    /** @var MockHandler 伪造的后台响应队列 */
    protected MockHandler $mock;

    /** @var string 测试期间被顶掉的 nginx.conf 路径 */
    protected string $nginxFile = '';

    protected function setUp(): void
    {
        $this->history = [];
        $this->mock = new MockHandler();
        $stack = HandlerStack::create($this->mock);
        $stack->push(Middleware::history($this->history));

        // 顶掉真实客户端、网关地址与登录令牌，使 request 全程不越界
        $this->setStatic('client', new Client(['handler' => $stack, 'http_errors' => false]), Gateway::class);
        $this->setStatic('gateway', 'http://gateway.test', Gateway::class);
        $this->setStatic('token', 'test-token', Gateway::class);

        // 登录凭据走 .env，这里直接写内存
        Env::set('GATEWAY_USER', 'admin');
        Env::set('GATEWAY_PASS', 'secret');

        // 同源判断会读 nginx.conf 的 proxy_pass 端口，指向不存在的文件以固定默认端口 39000
        $this->nginxFile = rtrim(str_replace('\\', '/', sys_get_temp_dir()), '/') . '/xbcode-no-nginx.conf';
        $this->setStatic('file', $this->nginxFile, Nginx::class);
    }

    protected function tearDown(): void
    {
        $this->setStatic('client', null, Gateway::class);
        $this->setStatic('gateway', '', Gateway::class);
        $this->setStatic('token', '', Gateway::class);

        Env::set('GATEWAY_USER', '');
        Env::set('GATEWAY_PASS', '');

        $this->setStatic('file', '', Nginx::class);
    }

    // ------------------------------------------------------------ 请求组装与响应校验

    /**
     * 按「网关地址 + 接口路径」拼接 URL，并带上 Accept 与 Authorization 头
     */
    public function testRequestBuildsUrlAndDefaultHeaders(): void
    {
        $this->respond(['web_name' => '积木云']);

        $data = Gateway::request('GET', '/xbAdmin/admin/Config/detail', [
            'query' => ['plugin' => 'demo', 'group' => 'basic'],
        ]);

        $this->assertSame(['web_name' => '积木云'], $data);

        $request = $this->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('gateway.test', $request->getUri()->getHost());
        $this->assertSame('/xbAdmin/admin/Config/detail', $request->getUri()->getPath());
        $this->assertSame(['plugin' => 'demo', 'group' => 'basic'], $this->lastQuery());
        $this->assertSame('application/json', $request->getHeaderLine('Accept'));
        $this->assertSame('Bearer test-token', $request->getHeaderLine('Authorization'));
        $this->assertSame(1, $this->sentCount());
    }

    /**
     * 请求体与自定义头原样透传给 HTTP 客户端
     */
    public function testRequestPassesBodyAndExtraHeaders(): void
    {
        $this->respond([]);

        Gateway::request('POST', '/xbAdmin/admin/Config/update', [
            'json' => ['plugin' => 'demo', 'name' => 'web_name', 'value' => '积木云'],
            'headers' => ['X-Test' => '1'],
        ]);

        $request = $this->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringContainsString('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertSame('1', $request->getHeaderLine('X-Test'));
        // 默认头不被覆盖
        $this->assertSame('application/json', $request->getHeaderLine('Accept'));
        $this->assertSame([
            'plugin' => 'demo',
            'name' => 'web_name',
            'value' => '积木云',
        ], $this->lastJsonBody());
    }

    /**
     * $auth 为 false 时不带登录令牌（登录接口自身即如此）
     */
    public function testRequestOmitsAuthorizationWhenAuthDisabled(): void
    {
        $this->respond([]);

        Gateway::request('POST', '/xbAdmin/admin/Publics/login', [
            'json' => ['username' => 'admin', 'password' => 'secret'],
        ], false);

        $this->assertFalse($this->lastRequest()->hasHeader('Authorization'));
    }

    /**
     * 传输异常时抛异常，消息含请求地址与底层错误
     */
    public function testRequestThrowsOnTransportError(): void
    {
        $this->mock->append(new ConnectException(
            'Connection refused',
            new Request('GET', 'http://gateway.test/xbAdmin/admin/Config/detail')
        ));

        try {
            Gateway::request('GET', '/xbAdmin/admin/Config/detail');
            $this->fail('未抛出异常');
        } catch (RuntimeException $e) {
            $this->assertSame(
                '请求后台接口失败：http://gateway.test/xbAdmin/admin/Config/detail，Connection refused',
                $e->getMessage()
            );
        }
    }

    /**
     * 响应体不是 JSON 时抛格式错误
     */
    public function testRequestThrowsOnInvalidJsonBody(): void
    {
        $this->mock->append(new Response(200, ['Content-Type' => 'text/html'], '<html>502</html>'));

        try {
            Gateway::request('GET', '/xbAdmin/admin/Dict/detail');
            $this->fail('未抛出异常');
        } catch (RuntimeException $e) {
            $this->assertSame(
                '请求后台接口失败，返回格式错误：http://gateway.test/xbAdmin/admin/Dict/detail',
                $e->getMessage()
            );
        }
    }

    /**
     * status 非 0 时按业务失败抛出，消息使用默认前缀与后台 msg
     */
    public function testRequestThrowsOnBusinessError(): void
    {
        $this->mock->append(new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'status' => 400,
            'msg' => '配置字段不存在：web_name',
        ], JSON_UNESCAPED_UNICODE)));

        try {
            Gateway::request('GET', '/xbAdmin/admin/Config/detail');
            $this->fail('未抛出异常');
        } catch (RuntimeException $e) {
            $this->assertSame('后台接口返回错误：配置字段不存在：web_name', $e->getMessage());
        }
    }

    /**
     * 业务错误未返回 msg 时回退到「未知错误」
     */
    public function testBusinessErrorFallsBackToUnknownMessage(): void
    {
        $this->mock->append(new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'status' => 500,
        ], JSON_UNESCAPED_UNICODE)));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('后台接口返回错误：未知错误');

        Gateway::request('GET', '/xbAdmin/admin/Config/detail');
    }

    /**
     * 自定义上下文与业务错误前缀（Upload 场景）
     */
    public function testCustomContextAndErrorPrefixes(): void
    {
        // 业务错误
        $this->mock->append(new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'status' => 400,
            'msg' => '文件类型不允许',
        ], JSON_UNESCAPED_UNICODE)));
        try {
            Gateway::request('POST', '/xbAdmin/admin/Upload/upload', [], true, '上传文件', '上传文件失败');
            $this->fail('未抛出异常');
        } catch (RuntimeException $e) {
            $this->assertSame('上传文件失败：文件类型不允许', $e->getMessage());
        }

        // 传输错误
        $this->mock->append(new ConnectException(
            'timed out',
            new Request('POST', 'http://gateway.test/xbAdmin/admin/Upload/upload')
        ));
        try {
            Gateway::request('POST', '/xbAdmin/admin/Upload/upload', [], true, '上传文件', '上传文件失败');
            $this->fail('未抛出异常');
        } catch (RuntimeException $e) {
            $this->assertSame(
                '上传文件失败：http://gateway.test/xbAdmin/admin/Upload/upload，timed out',
                $e->getMessage()
            );
        }

        // 格式错误
        $this->mock->append(new Response(200, [], 'not json'));
        try {
            Gateway::request('POST', '/xbAdmin/admin/Upload/upload', [], true, '上传文件', '上传文件失败');
            $this->fail('未抛出异常');
        } catch (RuntimeException $e) {
            $this->assertSame(
                '上传文件失败，返回格式错误：http://gateway.test/xbAdmin/admin/Upload/upload',
                $e->getMessage()
            );
        }
    }

    /**
     * 响应缺少 data 时返回空数组
     */
    public function testRequestReturnsEmptyArrayWhenDataMissing(): void
    {
        $this->mock->append(new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'status' => 0,
            'msg' => '',
        ], JSON_UNESCAPED_UNICODE)));

        $this->assertSame([], Gateway::request('GET', '/xbAdmin/admin/Config/detail'));
    }

    // ------------------------------------------------------------ 登录换令牌

    /**
     * 令牌为空时先以超管账号登录换取 JWT，再带令牌发起业务请求
     */
    public function testRequestLogsInWhenTokenMissing(): void
    {
        $this->setStatic('token', '', Gateway::class);
        $this->respondLogin('jwt-abc');
        $this->respond(['ok' => true]);

        $data = Gateway::request('GET', '/xbAdmin/admin/Config/detail');

        $this->assertSame(['ok' => true], $data);
        $this->assertSame(2, $this->sentCount());

        $login = $this->history[0]['request'];
        $this->assertSame('POST', $login->getMethod());
        $this->assertSame('/xbAdmin/admin/Publics/login', $login->getUri()->getPath());
        $this->assertFalse($login->hasHeader('Authorization'));
        $this->assertSame(['username' => 'admin', 'password' => 'secret'], $this->jsonBody($login));

        $business = $this->history[1]['request'];
        $this->assertSame('Bearer jwt-abc', $business->getHeaderLine('Authorization'));
        // 令牌被缓存
        $this->assertSame('jwt-abc', $this->getStatic('token', Gateway::class));
    }

    /**
     * 令牌已存在时直接复用，不触发登录
     */
    public function testRequestReusesCachedToken(): void
    {
        $this->respond([]);
        $this->respond([]);

        Gateway::request('GET', '/xbAdmin/admin/Config/detail');
        Gateway::request('GET', '/xbAdmin/admin/Dict/detail');

        $this->assertSame(2, $this->sentCount());
        foreach ($this->history as $item) {
            $this->assertSame('Bearer test-token', $item['request']->getHeaderLine('Authorization'));
        }
    }

    /**
     * 缺少超管账号配置时抛异常，且不发起任何请求
     */
    public function testLoginThrowsWhenCredentialsMissing(): void
    {
        $this->setStatic('token', '', Gateway::class);
        Env::set('GATEWAY_USER', '');
        Env::set('GATEWAY_PASS', '');

        try {
            Gateway::request('GET', '/xbAdmin/admin/Config/detail');
            $this->fail('未抛出异常');
        } catch (RuntimeException $e) {
            $this->assertSame('请在 .env 中配置 GATEWAY_USER 与 GATEWAY_PASS（后台超管账号）', $e->getMessage());
        }

        $this->assertSame(0, $this->sentCount());
    }

    /**
     * 登录成功但未返回 access_token 时抛异常
     */
    public function testLoginThrowsWhenTokenNotReturned(): void
    {
        $this->setStatic('token', '', Gateway::class);
        $this->respond([]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('登录后台失败，未获取到访问令牌');

        Gateway::request('GET', '/xbAdmin/admin/Config/detail');
    }

    // ------------------------------------------------------------ 网关地址

    /**
     * 未配置 GATEWAY_URL 时抛异常
     */
    public function testGatewayMissingThrows(): void
    {
        $this->setStatic('gateway', '', Gateway::class);
        Env::set('GATEWAY_URL', '');

        try {
            Gateway::request('GET', '/xbAdmin/admin/Config/detail');
            $this->fail('未抛出异常');
        } catch (RuntimeException $e) {
            $this->assertSame('请在 .env 中配置 GATEWAY_URL（后台访问地址）', $e->getMessage());
        }

        $this->assertSame(0, $this->sentCount());
    }

    /**
     * 网关地址去掉首尾空格与结尾斜杠后缓存
     */
    public function testGatewayIsTrimmedAndCached(): void
    {
        $this->setStatic('gateway', '', Gateway::class);
        Env::set('GATEWAY_URL', '  http://127.0.0.1:1234/  ');
        $this->respond([]);

        Gateway::request('GET', '/xbAdmin/admin/Config/detail');

        $this->assertSame('http://127.0.0.1:1234', $this->lastRequest()->getUri()->getScheme() . '://' . $this->lastRequest()->getUri()->getAuthority());
        $this->assertSame('http://127.0.0.1:1234', $this->getStatic('gateway', Gateway::class));
    }

    // ------------------------------------------------------------ 同源判断

    /**
     * 网关指向本机且端口与本应用一致（或未显式指定端口）时视为同源
     */
    #[DataProvider('selfHostedProvider')]
    public function testSelfHosted(string $gateway, bool $expected): void
    {
        $this->setStatic('gateway', $gateway, Gateway::class);

        $this->assertSame($expected, Gateway::selfHosted());
    }

    public static function selfHostedProvider(): array
    {
        return [
            '本机无端口' => ['http://127.0.0.1', true],
            'localhost 无端口' => ['http://localhost', true],
            '本机默认端口' => ['http://127.0.0.1:39000', true],
            '本机其它端口' => ['http://127.0.0.1:1234', false],
            '远程域名' => ['https://api.example.com', false],
            '远程主机带端口' => ['http://10.0.0.5:39000', false],
        ];
    }

    // ------------------------------------------------------------ 测试辅助

    /**
     * 往响应队列追加一条成功响应
     */
    protected function respond(array $data): void
    {
        $this->mock->append(new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'msg' => '',
            'status' => 0,
            'data' => $data,
        ], JSON_UNESCAPED_UNICODE)));
    }

    /**
     * 往响应队列追加一条登录成功响应
     */
    protected function respondLogin(string $token): void
    {
        $this->respond(['access_token' => $token]);
    }

    /**
     * 已发送的请求数
     */
    protected function sentCount(): int
    {
        return count($this->history);
    }

    /**
     * 最后一次请求
     */
    protected function lastRequest(): RequestInterface
    {
        $this->assertNotEmpty($this->history, '没有捕获到任何请求');
        return $this->history[array_key_last($this->history)]['request'];
    }

    /**
     * 最后一次请求的 JSON 请求体
     */
    protected function lastJsonBody(): array
    {
        return $this->jsonBody($this->lastRequest());
    }

    /**
     * 指定请求的 JSON 请求体
     */
    protected function jsonBody(RequestInterface $request): array
    {
        return (array) json_decode((string) $request->getBody(), true);
    }

    /**
     * 最后一次请求的查询参数
     */
    protected function lastQuery(): array
    {
        parse_str($this->lastRequest()->getUri()->getQuery(), $query);
        return $query;
    }

    /**
     * 写受保护静态属性，默认写 Gateway，可指定其它类
     */
    protected function setStatic(string $name, mixed $value, ?string $class = null): void
    {
        (new ReflectionProperty($class ?? Gateway::class, $name))->setValue(null, $value);
    }

    /**
     * 读受保护静态属性，默认读 Gateway，可指定其它类
     */
    protected function getStatic(string $name, ?string $class = null): mixed
    {
        return (new ReflectionProperty($class ?? Gateway::class, $name))->getValue();
    }
}