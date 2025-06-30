<?php
namespace plugin\xbCode\app\middleware;

use plugin\xbCode\api\Install;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use plugin\xbCode\utils\trait\JsonTrait;

/**
 * 权限中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbMiddleware implements MiddlewareInterface
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
    public function process(Request $request, callable $handler): Response
    {
        // 检测是否安装
        if (!Install::checked() && strpos($request->path(), '/install') === false) {
            return redirect('/install/');
        }
        // 继续向洋葱芯穿越，直至执行控制器得到响应
        $response = $handler($request);
        // 响应结果
        $result = $response->rawBody();
        $result = is_array($result) ? json_encode($result, 256) : $result;
        // 返回响应
        return $response;
    }
}