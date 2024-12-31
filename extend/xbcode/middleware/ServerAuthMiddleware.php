<?php
namespace xbcode\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use xbcode\trait\JsonTrait;
use Webman\MiddlewareInterface;

/**
 * 授权服务检查中间件
 * 没有授权则跳转到授权页面
 * 全站主项目包括插件使用
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ServerAuthMiddleware implements MiddlewareInterface
{
    // 引入JsonTrait
    use JsonTrait;
    
    /**
     * 处理请求
     * @param \Webman\Http\Request $request
     * @param callable $handler
     * @return \Webman\Http\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function process(Request $request, callable $handler) : Response
    {
        // 如果是options请求则返回一个空响应，否则继续向洋葱芯穿越，并得到一个响应
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        // 返回响应
        return $response;
    }
}