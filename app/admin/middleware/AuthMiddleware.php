<?php
declare(strict_types=1);

namespace app\admin\middleware;

use app\common\utils\AuthUtil;
use app\common\utils\JsonUtil;
use Closure;
use Exception;
use loong\oauth\facade\Auth;
use think\Request;

/**
 * 鉴权中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AuthMiddleware
{
    /**
     * 业务逻辑处理
     * @param mixed $request
     * @param \Closure $next
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function handle($request, Closure $next)
    {
        $request->token     = null;
        $request->userId    = null;
        $request->user      = null;
        try {
            self::canAccess($request);
        } catch (\Throwable $e) {
            return JsonUtil::failFul($e->getMessage(),$e->getCode() ?: 404);
        }
        $response = $next($request);
        return $response;
    }

    /**
     * 权限检测
     * @param \think\Request $request
     * @throws \Exception
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function canAccess(Request $request): bool
    {
        $control  = 'Index';
        $action  = 'index';
        $pathinfo = array_filter(explode('/',$request->pathinfo()));
        if (isset($pathinfo[0])) {
            $control = ucfirst($pathinfo[0]);
        }
        if (isset($pathinfo[1])) {
            $action = $pathinfo[1];
        }
        // 无控制器地址
        if (!$control) {
            throw new Exception('无效控制器');
        }
        // 获取控制器鉴权信息
        $class = app()->getNamespace() . '\\controller\\' . $control . 'Controller';
        $class = new \ReflectionClass($class);
        $properties = $class->getDefaultProperties();
        $NotLogin = $properties['NotLogin'] ?? [];
        $NotAuth = $properties['NotAuth'] ?? [];

        // 不需要登录
        if (in_array($action, $NotLogin)) {
            return true;
        }
        // 获取登录信息
        $authorization = $request->header('Authorization', '');
        if (empty($authorization)) {
            throw new Exception('请先登录', 12000);
        }
        // 获取用户信息
        try {
            $token      = str_replace('Bearer ', '', $authorization);
            $user       = Auth::setExpire(3600*24*7)->decrypt($token);
        } catch (\Throwable $e) {
            throw new Exception('登录已过期，请重新登录', 12000);
        }
        if (!$user) {
            throw new Exception('用户信息获取失败', 12000);
        }
        $request->token     = $authorization;
        $request->userId    = $user['id'];
        $request->user      = $user;
        # 验证渠道状态
        if ($user['status'] === '10') {
            throw new Exception('该用户已被禁用，请联系管理员', 12000);
        }
        // 不需要鉴权
        if (in_array($action, $NotAuth)) {
            return true;
        }
        if (empty($user['is_system'])) {
            throw new Exception('操作权限出错，请重新登录');
        }
        // 系统级部门，不需要鉴权
        if ($user['is_system'] === '20') {
            return true;
        }
        $path = "{$control}/{$action}";
        if (!AuthUtil::canAuth((int) $user['id'],$path)) {
            throw new Exception('没有该操作权限');
        }
        return true;
    }
}