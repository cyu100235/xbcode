<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\admin\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use plugin\xbCode\api\AdminLogApi;
use plugin\xbCode\utils\FrameUtil;

/**
 * 管理员操作日志中间件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminLogMiddleware implements MiddlewareInterface
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
        // 非后台写操作直接放行，避免 user/api/home 等模块白白穿越本中间件
        if (!$this->isRecordable($request)) {
            return $handler($request);
        }
        // 执行请求，先得到响应便于记录操作结果
        $response = $handler($request);
        // 记录管理员操作日志（内部已做异常兜底，不影响业务）
        AdminLogApi::make()->record($request, $response);
        // 返回响应
        return $response;
    }

    /**
     * 是否属于需要记录的后台写操作
     * @param Request $request
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function isRecordable(Request $request): bool
    {
        // 框架未安装
        if (!FrameUtil::checked()) {
            return false;
        }
        // 仅记录后台操作
        if ($request->app !== 'admin') {
            return false;
        }
        // 仅记录增删改操作，GET不记录
        return in_array(strtoupper($request->method()), ['POST', 'PUT', 'DELETE'], true);
    }
}