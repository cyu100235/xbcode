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
use plugin\xbCode\api\AdminApi;
use plugin\xbCode\api\AdminSession;
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
        // 校验密码指纹，密码被修改后强制下线
        $this->validatePassword($request->uid, $user['pwd_sign'] ?? '');
        // 获取管理员角色ID
        $roleId = $user['role_id'] ?? '';
        if (empty($roleId)) {
            throw new ExceptionUnauthorized('管理员角色标识参数错误');
        }
        $request->role_id = $roleId;
        // 校验登录会话，支持单点登录互踢与「登录令牌」踢出登录
        $this->validateSession((int) $request->uid, (string) ($user['session'] ?? ''), (int) $roleId);
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
        // 验证管理员是否有操作权限
        $permit = AdminApi::checkAuth(
            (int) $roleId,
            $pathInfo['plugin'],
            $pathInfo['module'],
            $pathInfo['controller'],
            $pathInfo['action']
        );
        if (!$permit) {
            throw new ExceptionForbidden('您没有操作权限');
        }
    }

    /**
     * 校验令牌中的密码指纹
     * 指纹取自缓存（按 CACHE_TYPE 走 file / redis），仅缓存未命中时才回源数据库，
     * 密码被修改后指纹不一致，强制下线重新登录
     * @param int|string $uid 管理员ID
     * @param string $sign 令牌中的密码指纹
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function validatePassword($uid, string $sign)
    {
        $passwordSign = AdminApi::getPasswordSign($uid);
        if ($passwordSign === null) {
            throw new ExceptionUnauthorized('管理员账号不存在，请重新登录');
        }
        if ($passwordSign !== $sign) {
            throw new ExceptionUnauthorized('登录密码已修改，请重新登录');
        }
    }

    /**
     * 校验登录会话
     * 会话数据来自缓存（按 CACHE_TYPE 走 file / redis），不查询数据库；
     * 会话被管理员踢出、或角色开启单点登录后原会话被新登录挤下线时，强制重新登录
     * @param int $uid 管理员ID
     * @param string $session 令牌中的会话标识
     * @param int $roleId 管理员角色ID
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function validateSession(int $uid, string $session, int $roleId)
    {
        $message = AdminSession::verify($uid, $session, AdminSession::roleSingleLogin($roleId));
        if ($message !== '') {
            throw new ExceptionUnauthorized($message);
        }
    }
}