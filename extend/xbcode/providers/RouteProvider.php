<?php

namespace xbcode\providers;

use Webman\Route;

/**
 * 站点配置提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class RouteProvider
{
    /**
     * 获取路由配置
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get()
    {
        // 设置默认路由
        Route::get(
            '/',
            [\app\index\controller\IndexController::class, 'index']
        );
        // 总后台配置项
        Route::any(
            '/backend/Setting/config/{path:.+}',
            [\app\backend\controller\SettingController::class, 'config']
        );
        // 子后台配置项
        Route::any(
            '/admin/Setting/config/{path:.+}',
            [\app\admin\controller\SettingController::class, 'config']
        );
        // 插件配置项
        Route::any(
            '/app/:plugin/Setting/config/{path:.+}',
            [\app\admin\controller\SettingController::class, 'config']
        );
    }
}