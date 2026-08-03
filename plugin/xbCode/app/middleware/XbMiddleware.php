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
use plugin\xbCode\utils\FrameUtil;

/**
 * 权限中间件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class XbMiddleware implements MiddlewareInterface
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
        // 检测是否安装
        if (!FrameUtil::checked() && !str_contains($request->path(), '/install')) {
            return redirect('/install/');
        }
        // 继续向洋葱芯穿越，直至执行控制器得到响应
        $response = $handler($request);
        // 返回响应
        return $response;
    }
}