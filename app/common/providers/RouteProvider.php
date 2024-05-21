<?php
namespace app\common\providers;

use app\common\service\CloudSerivce;
use app\common\utils\InstallUtil;
use app\model\AdminRule;
use support\Request;
use think\facade\Cache;
use Webman\Route;
use Exception;

/**
 * 路由服务提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class RouteProvider
{
    /**
     * 注册路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function regBaseRoute(bool $disabledDefault = true)
    {
        // 禁止默认路由
        if ($disabledDefault) {
            Route::disableDefaultRoute();
        }
        // 注册首页路由
        Route::get('/', 'app\controller\IndexController@index');
        // 渲染视图
        Route::get('/install/', 'app\controller\InstallController@index');
        // 获取协议
        Route::get('/install/protocol', '\app\controller\InstallController@protocol');
        // 环境检测
        Route::post('/install/environment', 'app\controller\InstallController@environment');
        // 重启服务
        Route::get('/install/reload', 'app\controller\InstallController@reload');
        // 安装完成
        Route::get('/install/complete', 'app\controller\InstallController@complete');
        // 注册文件路由
        Route::get('/install/assets/[{path:.+}]', function (Request $request, $path = '') {
            // 安全检查，避免url里 /../../../password 这样的非法访问
            if (strpos($path, '..') !== false) {
                return response('<h1>400 Bad Request</h1>', 400);
            }
            // 文件
            $installPath = app_path('common/view/install');
            $file        = "{$installPath}/assets/{$path}";
            if (!is_file($file)) {
                return response('<h1>404 Not Found</h1>', 404);
            }
            return response()->withFile($file);
        });
        // 检测路由类型
        if (!InstallUtil::hasInstall()) {
            // 安装进行
            Route::post('/install/install', 'app\controller\InstallController@install');
        } else {
            // 注册应用路由
            self::registerRoute();
        }
    }

    /**
     * 注册系统路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function registerRoute()
    {
        // 模块名称
        $moduleName = config('admin.module_name', '');
        if (!$moduleName) {
            throw new Exception('请配置后台模块名称');
        }
        // 注册后台路由
        Route::get("/{$moduleName}/", 'app\admin\controller\IndexController@index');
        $menus = Cache::get('admin_menus', []);
        foreach ($menus as $value) {
            if ($value['class'] && $value['path'] && $value['methods']) {
                Route::add(
                    $value['methods'],
                    "/{$value['path']}",
                    $value['class']
                );
            }
        }
        // 注册静态文件路由
        Route::get('/xbase/[{path:.+}]', function (Request $request, $path = '') {
            // 安全检查，避免url里 /../../../password 这样的非法访问
            if (strpos($path, '..') !== false) {
                return response('<h1>400 Bad Request</h1>', 400);
            }
            // 文件
            $installPath = app_path('common/view/admin');
            $file        = "{$installPath}/xbase/{$path}";
            if (!is_file($file)) {
                return response('<h1>404 Not Found</h1>', 404);
            }
            return response()->withFile($file);
        });
    }

    /**
     * 插件中间件
     * @return string[]
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pluginMiddleware()
    {
        $plugins = CloudSerivce::getLocalPlugin();
        $data = [];
        foreach ($plugins as $value) {
            if (!empty($value['name'])) {
                $data["plugin.{$value['name']}"] = [
                    \app\common\middleware\PluginsMiddleware::class
                ];
            }
        }
        return $data;
    }

    /**
     * 缓存菜单
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function cacheMenus()
    {
        $data = AdminRule::order('sort asc')->select()->toArray();
        foreach ($data as &$value) {
            $value['methods'] = explode(',', $value['methods']);
        }
        Cache::set('admin_menus', $data);
    }
}