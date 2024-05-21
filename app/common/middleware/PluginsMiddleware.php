<?php
namespace app\common\middleware;

use app\common\providers\ConfigProvider;
use app\common\utils\JsonUtil;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

/**
 * 鉴权中间件检测
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsMiddleware implements MiddlewareInterface
{
    use JsonUtil;

    /**
     * 处理请求
     * @param \Webman\Http\Request $request
     * @param callable $handler
     * @return \Webman\Http\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function process(Request $request, callable $handler): Response
    {
        // 获取数据
        $name = $request->plugin;
        $config = ConfigProvider::get($name);
        $request->pluginConfig = $config;
        // 鉴权前置钩子
        $response = $request->method() == 'OPTIONS' ? response('', 204) : $handler($request);
        // 鉴权后置钩子
        return $response;
    }
}