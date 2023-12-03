<?php
declare (strict_types = 1);

namespace app\common;

use Exception;
use think\Route;
use think\Service;

/**
 * 应用注册服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbService extends Service
{
    /**
     * 服务入口
     * @param \think\facade\Route $route
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function boot(Route $route)
    {
        # 监听服务
        $this->app->event->listen('HttpRun', function () use ($route) {
            # 注册路由
            $route->rule('base/:name/[:module]/[:control]/[:action]', function ($name, $module = '', $control = '', $action = '') {
                $module = $module ?: 'index';
                $control  = $control ? ucfirst($control) : config('route.default_controller', 'Index');
                $action   = $action ?: config('route.default_action', 'index');
                $isSuffix = config('route.controller_suffix', false);
                $suffix   = $isSuffix ? 'Controller' : '';
                $class    = "\\base\\{$name}\\app\\{$module}\\controller\\{$control}{$suffix}";
                if (!class_exists($class)) {
                    throw new Exception("应用控制器不存在：{$class}");
                }
                # 调度转发
                return \think\App::invokeMethod([$class, $action]);
            })
            ->middleware([
                \app\common\middleware\ServiceMiddleware::class
            ]);
        });
    }
}
