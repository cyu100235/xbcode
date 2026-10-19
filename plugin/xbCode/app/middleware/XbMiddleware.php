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
     * @param Request $request
     * @param callable $handler
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function process(Request $request, callable $handler): Response
    {
        // 检测是否安装（未安装时提示执行命令行安装命令）
        if (!FrameUtil::checked()) {
            // 返回 503，并渲染首页视图的安装提示分支
            return view('index/index', ['installed' => false], 'index', 'xbCode')->withStatus(503);
        }
        // 继续向洋葱芯穿越，直至执行控制器得到响应
        $response = $handler($request);
        // 返回响应
        return $response;
    }
}