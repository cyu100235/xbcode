<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */

use Webman\Exception\BusinessException;
use plugin\xbAdmin\exception\business\ExceptionValidate;

if (!function_exists('xbAdminPathInfo')) {
    /**
     * 解析当前请求的路由标识
     *
     * 权限比对以「模块/控制器/方法」为键，与 admin_rule.path 保持同一口径，
     * 例如 admin/AdminRole/index。
     * @param string $class 控制器类全名
     * @param string $action 方法名
     * @return array
     */
    function xbAdminPathInfo(string $class, string $action): array
    {
        $request = request();
        // 插件标识
        $plugin = (string) ($request->plugin ?? '');
        // 模块标识
        $module = (string) ($request->app ?? '');
        // 控制器后缀
        $suffix = (string) config("plugin.{$plugin}.app.controller_suffix", config('app.controller_suffix', 'Controller'));
        // 控制器名称
        $controller = basename(str_replace('\\', '/', $class));
        if ($suffix !== '') {
            $controller = str_replace($suffix, '', $controller);
        }
        // 权限比对路径
        $path = $module === '' ? "{$controller}/{$action}" : "{$module}/{$controller}/{$action}";
        return [
            'plugin' => $plugin,
            'module' => $module,
            'controller' => $controller,
            'action' => $action,
            'path' => $path,
            'uri' => $path,
            'class' => $class,
        ];
    }
}

if (!function_exists('xbValidate')) {
    /**
     * 执行数据验证
     * 验证器继承 Webman\Validation\Validator，失败时抛出框架异常，
     * 这里统一转换带前端通知事件的业务异常，保证错误信息一定能弹出。
     * @param string $class 验证器类名
     * @param array $data 待验证数据
     * @param string $scene 验证场景
     * @return array
     * @throws ExceptionValidate
     */
    function xbValidate(string $class, array $data, string $scene = ''): array
    {
        try {
            /** @var \Webman\Validation\Validator $validator */
            $validator = $class::make($data);
            if ($scene !== '') {
                $validator = $validator->withScene($scene);
            }
            return $validator->validate();
        } catch (Throwable $e) {
            if ($e instanceof ExceptionValidate) {
                throw $e;
            }
            if ($e instanceof BusinessException) {
                $message = $e->getMessage() !== '' ? $e->getMessage() : '提交的数据校验失败';
                throw new ExceptionValidate($message);
            }
            throw $e;
        }
    }
}
