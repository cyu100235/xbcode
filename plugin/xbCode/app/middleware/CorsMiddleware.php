<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 跨域中间件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CorsMiddleware implements MiddlewareInterface
{
    /**
     * 处理请求
     * @param \Webman\Http\Request $request
     * @param callable $handler
     * @return \Webman\Http\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function process(Request $request, callable $handler): Response
    {
        // 继续向洋葱芯穿越，直至执行控制器得到响应
        $response = $handler($request);
        // 设置跨域头
        $response->withHeaders([
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Origin' => $request->header('origin', '*'),
            'Access-Control-Allow-Methods' => $request->header('access-control-request-method', '*'),
            'Access-Control-Allow-Headers' => $request->header('access-control-request-headers', '*'),
        ]);
        // 返回响应
        return $response;
    }
}