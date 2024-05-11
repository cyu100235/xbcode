<?php
namespace app\middleware;

use app\utils\JsonUtil;
use Exception;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

/**
 * 插件鉴权中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginMiddleware implements MiddlewareInterface
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
        // 鉴权前置钩子
        $this->canAuth($request,$handler);
        // 鉴权前置钩子
        $response = $request->method() == 'OPTIONS' ? response('', 204) : $handler($request);
        // 鉴权后置钩子
        return $response;
    }

    /**
     * 用户鉴权
     * @param \Webman\Http\Request $request
     * @param callable $handler
     * @throws \Exception
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function canAuth(Request $request, callable $handler)
    {
        // 鉴权通过
        return true;
    }
}