<?php
namespace plugin\xbCode\app\admin\middleware;

use Exception;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;
use plugin\xbCode\app\model\AdminRole;
use plugin\xbCode\utils\trait\JsonTrait;

/**
 * 权限中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AuthMiddleware implements MiddlewareInterface
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
        try {
            // 权限检测
            $this->validateAuth($request);
        } catch (\Throwable $th) {
            // 抛出异常
            throw new Exception($th->getMessage(), $th->getCode());
        }
        // 返回空响应
        if ($request->method() === 'OPTIONS') {
            return response('');
        }
        // 继续向洋葱芯穿越，直至执行控制器得到响应
        $response = $handler($request);
        // 响应结果
        $result = $response->rawBody();
        $result = is_array($result) ? json_encode($result, 256) : $result;
        // 返回响应
        return $response;
    }

    /**
     * 权限检测
     * @param \Webman\Http\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function validateAuth(Request $request)
    {
        // 获取请求路径信息
        $pathInfo = xbPathInfo($request->path());
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
        $users = $request->session()->get('xbcode');
        if (empty($users)) {
            throw new Exception('请登录后再操作', 12000);
        }
        try {
            // 获取管理员ID
            $adminId = $users['id'];
            // 获取管理员角色ID
            $roleId = $users['role_id'];
            // 获取管理员账号
            $username = $users['username'];
            // 获取管理员状态
            $adminState = $users['state'];
            // 是否系统管理员
            $isAdmin = $users['is_system'];
            // 设置请求管理员ID
            $request->uid = $adminId;
            // 设置请求管理员账号
            $request->username = $username;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 12000);
        }
        if ($adminState === '10') {
            throw new Exception('账号已被禁用', 404);
        }
        print_r($isAdmin);
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
        if (!AdminRole::checkAuth($roleId, $pathInfo['uri'])) {
            throw new Exception('您没有操作权限', 403);
        }
    }
}