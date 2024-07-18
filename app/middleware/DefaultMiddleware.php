<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

/**
 * 默认中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DefaultMiddleware implements MiddlewareInterface
{
    /**
     * 处理请求
     * @param \Webman\Http\Request $request
     * @param callable $next
     * @return \Webman\Http\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function process(Request $request, callable $next): Response
    {
        /** @var Response $response */
        $response = $next($request);
        return $response;
    }
}