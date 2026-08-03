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

use Exception;
use Webman\Http\Request;
use Webman\Http\Response;
use Tinywan\Jwt\JwtToken;
use Webman\MiddlewareInterface;
use plugin\xbCode\api\AdminAuthApi;
use plugin\xbCode\exception\business\ExceptionForbidden;
use plugin\xbCode\exception\business\ExceptionUnauthorized;

/**
 * 后台权限鉴权中间件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AuthMiddleware implements MiddlewareInterface
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
        // 返回空响应
        if ($request->method() === 'OPTIONS') {
            return response('');
        }
        // 权限鉴权
        $this->validateAuth($request);
        // 继续向洋葱芯穿越，直至执行控制器得到响应
        $response = $handler($request);
        // 返回响应
        return $response;
    }

    /**
     * 权限检测
     * @param Request $request
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function validateAuth(Request $request)
    {
        // 获取请求路径信息
        $pathInfo = xbPathInfo($request->controller, $request->action);
        if (empty($pathInfo)) {
            return;
        }
        // 反射获取控制器属性
        $class = new \ReflectionClass($pathInfo['class']);
        $props = $class->getDefaultProperties();
        // 不需要登录的接口
        $noLogin = $props['noLogin'] ?? [];
        if (in_array($pathInfo['action'], $noLogin)) {
            return;
        }
        try {
            $user = JwtToken::getExtend();
            if (empty($user)) {
                throw new Exception('请登录后再操作');
            }
        } catch (\Throwable $th) {
            throw new ExceptionUnauthorized($th->getMessage());
        }
        // 获取管理员ID
        $request->uid = $user['id'] ?? '';
        if (empty($request->uid)) {
            throw new ExceptionUnauthorized('管理员标识参数错误');
        }
        // 获取管理员角色ID
        $roleId = $user['role_id'] ?? '';
        if (empty($roleId)) {
            throw new ExceptionUnauthorized('管理员角色标识参数错误');
        }
        $request->role_id = $roleId;
        // 获取管理员账号
        $username = $user['username'] ?? '';
        // 设置请求管理员账号
        $request->username = $username;
        if (empty($request->username)) {
            throw new ExceptionUnauthorized('管理员账号参数错误');
        }
        // 获取管理员状态
        $adminState = $user['state'] ?? '';
        if (empty($adminState)) {
            throw new ExceptionUnauthorized('管理员状态参数错误');
        }
        // 是否系统管理员
        $isAdmin = $user['is_system'] ?? '';
        if (empty($isAdmin)) {
            throw new ExceptionUnauthorized('管理员系统标识参数错误');
        }
        if ($adminState === '10') {
            throw new ExceptionForbidden('账号已被禁用');
        }
        // 系统管理员不验证权限
        if ($isAdmin === '20') {
            return;
        }
        // 不需要验证权限的接口
        $noAuth = $props['noAuth'] ?? [];
        if (in_array($pathInfo['action'], $noAuth)) {
            return;
        }
        // 检测是否有操作权限
        if (!AdminAuthApi::make()->Authentication($roleId, $pathInfo['uri'])) {
            throw new ExceptionForbidden('您没有操作权限');
        }
    }
}