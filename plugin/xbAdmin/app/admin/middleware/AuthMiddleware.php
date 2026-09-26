<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\app\admin\middleware;

use Throwable;
use ReflectionClass;
use support\Request;
use Webman\Http\Response;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\app\model\Admin;
use plugin\xbAdmin\utils\TokenUtil;
use plugin\xbAdmin\api\AdminAuthApi;
use plugin\xbAdmin\exception\business\ExceptionForbidden;
use plugin\xbAdmin\exception\business\ExceptionUnauthorized;

/**
 * 后台登录与权限校验中间件
 *
 * 该中间件通过 plugin/xbAdmin/config/middleware.php 的 'admin' 键
 * 绑定到 xbAdmin 的 admin 模块，不污染其他插件。
 *
 * 校验流程：
 * 1. 预检请求直接放行
 * 2. 控制器 $noLogin 白名单放行（登录、验证码、SPA 入口等）
 * 3. 解析 JWT 取管理员标识
 * 4. 回查数据库获取最新的状态、角色、超管标识，避免改权限后令牌仍是旧值
 * 5. 超级管理员放行，其余按角色权限列表比对「模块/控制器/方法」
 */
class AuthMiddleware
{
    /**
     * 请求处理
     * @param Request $request
     * @param callable $handler
     * @return Response
     */
    public function process(Request $request, callable $handler): Response
    {
        if ($request->method() === 'OPTIONS') {
            return response('');
        }
        $this->validateAuth($request);
        return $handler($request);
    }

    /**
     * 执行登录态与权限校验
     * @param Request $request
     * @throws ExceptionUnauthorized
     * @throws ExceptionForbidden
     * @return void
     */
    protected function validateAuth(Request $request): void
    {
        $pathInfo = xbAdminPathInfo((string) $request->controller, (string) $request->action);
        if ($pathInfo['path'] === '') {
            return;
        }
        if ($this->isNoLogin($pathInfo)) {
            return;
        }
        $admin = $this->currentAdmin($request);
        if ((string) $admin['state'] === StateEnum::DISABLED['value']) {
            throw new ExceptionForbidden('该账号已被禁用，请联系管理员');
        }
        if ($this->isNoAuth($pathInfo)) {
            return;
        }
        if ((string) $admin['is_system'] === YesEnum::YES['value']) {
            return;
        }
        $roleId = (int) $admin['role_id'];
        $passed = AdminAuthApi::make()->Authentication($roleId, $pathInfo['path']);
        if (!$passed) {
            throw new ExceptionForbidden('您没有操作权限');
        }
    }

    /**
     * 获取当前登录管理员，并挂载到请求对象
     * @param Request $request
     * @throws ExceptionUnauthorized
     * @return Admin
     */
    protected function currentAdmin(Request $request): Admin
    {
        try {
            $token = TokenUtil::extend();
        } catch (Throwable $e) {
            throw new ExceptionUnauthorized('登录信息已失效，请重新登录');
        }
        $id = (int) (is_array($token) ? ($token['id'] ?? 0) : 0);
        if ($id <= 0) {
            throw new ExceptionUnauthorized('管理员标识参数错误');
        }
        $model = Admin::where('id', $id)->find();
        if (!$model) {
            throw new ExceptionUnauthorized('登录信息已失效，请重新登录');
        }
        $request->uid = (int) $model['id'];
        $request->role_id = (int) $model['role_id'];
        $request->username = (string) $model['username'];
        $request->is_system = (string) $model['is_system'];
        $user = $model->toArray();
        unset($user['password']);
        $request->user = $user;
        return $model;
    }

    /**
     * 判断方法是否免登录
     * @param array $pathInfo
     * @return bool
     */
    protected function isNoLogin(array $pathInfo): bool
    {
        return in_array($pathInfo['action'], $this->defaultList($pathInfo['class'], 'noLogin'), true);
    }

    /**
     * 判断方法是否免权限校验
     * @param array $pathInfo
     * @return bool
     */
    protected function isNoAuth(array $pathInfo): bool
    {
        return in_array($pathInfo['action'], $this->defaultList($pathInfo['class'], 'noAuth'), true);
    }

    /**
     * 读取控制器声明的属性默认值
     *
     * 这里用反射而不是实例化控制器，避免中间件阶段提前触发控制器构造逻辑。
     * @param string $class 控制器类名
     * @param string $property 属性名
     * @return array
     */
    protected function defaultList(string $class, string $property): array
    {
        try {
            $properties = (new ReflectionClass($class))->getDefaultProperties();
        } catch (Throwable $e) {
            return [];
        }
        $value = $properties[$property] ?? [];
        return is_array($value) ? $value : [];
    }
}
