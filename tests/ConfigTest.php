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
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use plugin\xbCode\api\Config;
use plugin\xbCode\api\Gateway;
use Psr\Http\Message\RequestInterface;
use ReflectionProperty;
use RuntimeException;

/**
 * Config 后台配置项客户端单元测试
 *
 * 只覆盖两件事：配置键的层级解析、分组配置值的缓存。
 * HTTP 层用 Guzzle MockHandler 顶掉，不发真实请求，也不依赖 .env 与后台服务。
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ConfigTest extends TestCase
{
    /** @var array 已发送的请求历史，由 Guzzle history 中间件写入 */
    protected array $history = [];

    /** @var MockHandler 伪造的后台响应队列 */
    protected MockHandler $mock;

    protected function setUp(): void
    {
        $this->history = [];
        $this->mock = new MockHandler();
        $stack = HandlerStack::create($this->mock);
        $stack->push(Middleware::history($this->history));

        // 顶掉真实客户端、网关地址与登录令牌（挂在 Gateway 上），使 get/set 全程不越界
        $this->setStatic('client', new Client(['handler' => $stack, 'http_errors' => false]), Gateway::class);
        $this->setStatic('gateway', 'http://gateway.test', Gateway::class);
        $this->setStatic('token', 'test-token', Gateway::class);
        $this->setStatic('cache', []);
    }

    protected function tearDown(): void
    {
        $this->setStatic('client', null, Gateway::class);
        $this->setStatic('gateway', '', Gateway::class);
        $this->setStatic('token', '', Gateway::class);
        $this->setStatic('cache', []);
    }

    // ------------------------------------------------------------ 键解析：读取

    /**
     * 键只有「插件名.分组名」时返回整组配置值
     */
    public function testGetReturnsWholeGroupWhenKeyHasNoFieldSegment(): void
    {
        $group = ['web_name' => '积木云', 'captcha_state' => '20'];
        $this->respond($group);

        $this->assertSame($group, Config::get('demo.basic'));

        $uri = $this->lastRequest()->getUri();
        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('gateway.test', $uri->getHost());
        $this->assertSame('/xbAdmin/admin/Config/detail', $uri->getPath());
        $this->assertSame(['plugin' => 'demo', 'group' => 'basic'], $this->lastQuery());
        $this->assertSame(1, $this->sentCount());
    }

    /**
     * 分组没有任何配置值时返回默认值
     */
    public function testGetReturnsDefaultWhenGroupIsEmpty(): void
    {
        $this->respond([]);

        $this->assertSame('DEF', Config::get('demo.basic', 'DEF'));
        $this->assertNull(Config::get('demo.basic'));
        $this->assertSame(1, $this->sentCount());
    }

    /**
     * 字段名之后的段全部作为取值层级，且不进入查询参数
     */
    public function testGetTraversesFieldAndNestedSegments(): void
    {
        $this->respond([
            'web_name' => '积木云',
            'list' => [['name' => 'A'], ['name' => 'B']],
            'conf' => ['db' => ['host' => '127.0.0.1', 'port' => 3306]],
        ]);

        $this->assertSame('积木云', Config::get('demo.basic.web_name'));
        $this->assertSame('B', Config::get('demo.basic.list.1.name'));
        $this->assertSame(3306, Config::get('demo.basic.conf.db.port'));

        // 三次读取共用一次请求，查询参数里只有插件名与分组名
        $this->assertSame(1, $this->sentCount());
        $this->assertSame(['plugin' => 'demo', 'group' => 'basic'], $this->lastQuery());
    }

    /**
     * 字段名不存在，或中间层级不是数组时返回默认值
     */
    public function testGetReturnsDefaultWhenPathCannotBeTraversed(): void
    {
        $this->respond(['web_name' => '积木云']);

        $this->assertSame('DEF', Config::get('demo.basic.not_exists', 'DEF'));
        $this->assertNull(Config::get('demo.basic.not_exists'));
        // web_name 是标量，再往下取只会拿到默认值
        $this->assertSame('DEF', Config::get('demo.basic.web_name.0', 'DEF'));
        $this->assertSame(1, $this->sentCount());
    }

    /**
     * 字段值本身是 null 时按「已取到」处理，不回落默认值
     */
    public function testGetTreatsExistingNullValueAsHit(): void
    {
        $this->respond(['web_name' => null]);

        $this->assertNull(Config::get('demo.basic.web_name', 'DEF'));
        $this->assertSame(1, $this->sentCount());
    }

    /**
     * 解析键时去掉首尾空格与空白段
     */
    public function testGetIgnoresSpacesAndBlankSegments(): void
    {
        $this->respond(['conf' => ['db' => ['host' => '127.0.0.1']]]);

        $this->assertSame('127.0.0.1', Config::get(' demo . basic .. conf.db.host '));
        $this->assertSame(['plugin' => 'demo', 'group' => 'basic'], $this->lastQuery());
        $this->assertSame(1, $this->sentCount());
    }

    /**
     * 键不合法时抛异常
     */
    #[DataProvider('invalidKeyProvider')]
    public function testGetThrowsWhenKeyIsInvalid(string $key, string $message): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage($message);

        Config::get($key);
    }

    public static function invalidKeyProvider(): array
    {
        return [
            '空键' => ['', '配置键格式错误'],
            '只有插件名' => ['demo', '配置键格式错误'],
            '只有分隔符' => ['...', '配置键格式错误'],
            '插件标识含符号' => ['de!mo.basic.web_name', '插件标识不合法'],
            '插件标识含空格' => ['de mo.basic.web_name', '插件标识不合法'],
            '分组标识含符号' => ['demo.ba!sic.web_name', '分组标识不合法'],
            // 分组标识即 setting 文件名，只允许字母数字、下划线与短横线
            '分组标识为中文' => ['demo.分组.web_name', '分组标识不合法'],
        ];
    }

    // ------------------------------------------------------------ 键解析：写入

    /**
     * 写入字段值：请求体只带插件名、分组名、字段名与值
     */
    public function testSetWritesFieldValue(): void
    {
        $this->respond([]);

        $this->assertTrue(Config::set('demo.basic.web_name', '积木云'));

        $request = $this->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('gateway.test', $request->getUri()->getHost());
        $this->assertSame('/xbAdmin/admin/Config/update', $request->getUri()->getPath());
        $this->assertStringContainsString('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertSame([
            'plugin' => 'demo',
            'group' => 'basic',
            'name' => 'web_name',
            'value' => '积木云',
        ], $this->lastJsonBody());
    }

    /**
     * 写入带层级的键：先读字段当前值，按层级合并后整值提交
     */
    public function testSetMergesNestedSegmentsIntoExistingValue(): void
    {
        $this->respond(['multi' => ['a' => ['x' => 1, 'y' => 2], 'b' => 'B']]);
        $this->respond([]);

        $this->assertTrue(Config::set('demo.basic.multi.a.y', 9));

        // 第一次是读取字段当前值，第二次才是写入
        $this->assertSame(2, $this->sentCount());
        $this->assertSame('GET', $this->history[0]['request']->getMethod());
        $this->assertSame([
            'plugin' => 'demo',
            'group' => 'basic',
            'name' => 'multi',
            'value' => ['a' => ['x' => 1, 'y' => 9], 'b' => 'B'],
        ], $this->lastJsonBody());
    }

    /**
     * 写入带层级的数组下标
     */
    public function testSetMergesNestedSegmentsIntoArrayIndex(): void
    {
        $this->respond(['list' => [['name' => 'A'], ['name' => 'B']]]);
        $this->respond([]);

        $this->assertTrue(Config::set('demo.basic.list.1.name', 'C'));

        $this->assertSame([['name' => 'A'], ['name' => 'C']], $this->lastJsonBody()['value']);
    }

    /**
     * 字段当前值不是数组时，按层级重建数组
     */
    public function testSetRebuildsNestedPathWhenCurrentValueIsScalar(): void
    {
        $this->respond(['multi' => 'scalar']);
        $this->respond([]);

        $this->assertTrue(Config::set('demo.basic.multi.a.b', 'v'));

        $this->assertSame(['a' => ['b' => 'v']], $this->lastJsonBody()['value']);
    }

    /**
     * 字段不存在时，按层级从空数组创建
     */
    public function testSetCreatesNestedPathWhenFieldMissing(): void
    {
        $this->respond([]);
        $this->respond([]);

        $this->assertTrue(Config::set('demo.basic.multi.a.b', 'v'));

        $this->assertSame(['a' => ['b' => 'v']], $this->lastJsonBody()['value']);
    }

    /**
     * 键缺少字段名时直接抛异常，不发起任何请求
     */
    public function testSetThrowsWhenFieldSegmentIsMissing(): void
    {
        try {
            Config::set('demo.basic', 'v');
            $this->fail('未抛出异常');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('写入配置失败，缺少字段名：demo.basic', $e->getMessage());
        }

        $this->assertSame(0, $this->sentCount());
    }

    // ------------------------------------------------------------ 缓存

    /**
     * 同一分组的配置值只请求一次
     */
    public function testGroupValueIsRequestedOnlyOnce(): void
    {
        $this->respond(['web_name' => '第一次']);
        $this->respond(['web_name' => '第二次']);

        $this->assertSame('第一次', Config::get('demo.basic.web_name'));
        $this->assertSame('第一次', Config::get('demo.basic.web_name'));
        $this->assertSame('缺省', Config::get('demo.basic.not_exists', '缺省'));
        // 队列里第二条响应没有被取走，说明确实命中了缓存
        $this->assertSame(1, $this->sentCount());
        $this->assertSame(1, $this->mock->count());
    }

    /**
     * 缓存按「插件名.分组名」隔离
     */
    public function testCacheIsIsolatedPerPluginAndGroup(): void
    {
        $this->respond(['v' => 'basic']);
        $this->respond(['v' => 'other']);
        $this->respond(['v' => 'other-plugin']);

        $this->assertSame('basic', Config::get('demo.basic.v'));
        $this->assertSame('other', Config::get('demo.other.v'));
        $this->assertSame('other-plugin', Config::get('other.basic.v'));

        $this->assertSame(3, $this->sentCount());
        $this->assertSame(['demo.basic', 'demo.other', 'other.basic'], array_keys($this->getStatic('cache')));
    }

    /**
     * 写入后清掉所在分组的缓存，下次读取重新请求
     */
    public function testSetClearsGroupCache(): void
    {
        $this->respond(['web_name' => '旧值']);
        $this->respond([]);
        $this->respond(['web_name' => '新值']);

        $this->assertSame('旧值', Config::get('demo.basic.web_name'));
        $this->assertTrue(Config::set('demo.basic.web_name', '新值'));
        $this->assertSame('新值', Config::get('demo.basic.web_name'));

        $this->assertSame(3, $this->sentCount());
        $this->assertSame(['demo.basic'], array_keys($this->getStatic('cache')));
        $this->assertSame(['web_name' => '新值'], $this->getStatic('cache')['demo.basic']);
    }

    /**
     * 写入只清所在分组的缓存，其他分组不受影响
     */
    public function testSetKeepsOtherGroupCache(): void
    {
        $this->respond(['v' => 'basic']);
        $this->respond(['v' => 'other']);
        $this->respond([]);

        $this->assertSame('basic', Config::get('demo.basic.v'));
        $this->assertSame('other', Config::get('demo.other.v'));
        $this->assertTrue(Config::set('demo.basic.v', 'changed'));

        // 其他分组仍命中缓存，不会再发请求
        $this->assertSame('other', Config::get('demo.other.v'));
        $this->assertSame(3, $this->sentCount());
        $this->assertSame(['demo.other'], array_keys($this->getStatic('cache')));
    }

    /**
     * 后台返回业务错误时抛异常，且失败结果不写入缓存
     */
    public function testBusinessErrorResponseIsNotCached(): void
    {
        $this->mock->append(new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'status' => 400,
            'msg' => '配置字段不存在：web_name',
        ], JSON_UNESCAPED_UNICODE)));

        try {
            Config::get('demo.basic.web_name');
            $this->fail('未抛出异常');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('后台接口返回错误：配置字段不存在：web_name', $e->getMessage());
        }

        $this->assertSame([], $this->getStatic('cache'));
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
        return (array) json_decode((string) $this->lastRequest()->getBody(), true);
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
     * 写受保护静态属性，默认写 Config，可指定其它类（如 Gateway）
     */
    protected function setStatic(string $name, mixed $value, ?string $class = null): void
    {
        (new ReflectionProperty($class ?? Config::class, $name))->setValue(null, $value);
    }

    /**
     * 读受保护静态属性，默认读 Config，可指定其它类（如 Gateway）
     */
    protected function getStatic(string $name, ?string $class = null): mixed
    {
        return (new ReflectionProperty($class ?? Config::class, $name))->getValue();
    }
}