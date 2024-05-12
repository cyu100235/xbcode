<?php
namespace app\providers;

use app\model\AdminRule;
use app\model\Plugins;
use app\utils\InstallUtil;
use Exception;
use support\Request;
use think\facade\Cache;
use Webman\Route;

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
        Route::get('/install/protocol', 'app\controller\InstallController@protocol');
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
            $installPath = app_path('view/install');
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
            self::registerSystem();
            // 注册插件路由
            self::registerPlugin();
        }
    }

    /**
     * 注册插件路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function registerPlugin()
    {
        // 注册插件路由
        Route::any('/plugin/{name}[/{path}]', function (Request $request, $name = '', $path = '') {
            // 安全检查，避免url里 /../../../password 这样的非法访问
            if (strpos($name, '..') !== false || strpos($path, '..') !== false) {
                return response('<h1>400 Bad Request</h1>', 400);
            }
            if (!$name) {
                throw new Exception('插件标识参数错误');
            }
            // 检测插件是否安装
            $model = Plugins::where('name', $name)->find();
            if (!$model) {
                throw new Exception("该插件未安装");
            }
            // 检测插件是否启用
            if ($model['state'] !== '20') {
                throw new Exception("该插件未启用");
            }
            $control = 'Index';
            $action  = 'index';
            $data    = array_filter(explode('/', $path));
            if (isset ($data[0])) {
                $control = ucfirst($data[0]);
            }
            // 控制器后缀
            $suffix  = config('app.controller_suffix', '');
            $control = "{$control}{$suffix}";
            if (isset ($data[1])) {
                $action = $data[1];
            }
            // 检测是否存在控制器方法
            $class = "\\base\\{$name}\\controller\\{$control}";
            if (method_exists($class, $action)) {
                $class = new $class;
                return call_user_func([$class, $action], $request);
            }
            // 插件目录
            $pluginPath = base_path("base/{$name}");
            $path       = $path ? "/{$path}" : '';
            $dirPath    = "{$pluginPath}/public{$path}";
            // 检测是否存在默认文档
            $docements = [
                'index.html',
                'index.htm',
            ];
            foreach ($docements as $value) {
                $htmlPath = "{$dirPath}/{$value}";
                if (is_file($htmlPath)) {
                    return response()->withFile($htmlPath);
                }
            }
            return response('<h1>404 Plugin Not Found</h1>', 404);
        })->middleware([\app\middleware\PluginMiddleware::class]);
    }

    /**
     * 注册系统路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function registerSystem()
    {
        // 模块名称
        $moduleName = config('admin.module_name', '');
        if (!$moduleName) {
            throw new Exception('请配置后台模块名称');
        }
        $middleware = [];
        $path       = app_path("{$moduleName}/middleware.php");
        if (file_exists($path)) {
            $middleware = include $path;
        }
        // 中间件合并
        $middleware = array_merge([
            \app\middleware\AuthMiddleware::class
        ], $middleware);
        // 注册后台路由
        Route::get("/{$moduleName}/", 'app\admin\controller\IndexController@index');
        $menus = Cache::get('admin_menus', []);
        foreach ($menus as $value) {
            if ($value['class'] && $value['path'] && $value['methods']) {
                Route::add(
                    $value['methods'],
                    "/{$value['path']}",
                    $value['class']
                )->middleware($middleware);
            }
        }
        // 注册静态文件路由
        Route::get('/xbase/[{path:.+}]', function (Request $request, $path = '') {
            // 安全检查，避免url里 /../../../password 这样的非法访问
            if (strpos($path, '..') !== false) {
                return response('<h1>400 Bad Request</h1>', 400);
            }
            // 文件
            $installPath = app_path('view/admin');
            $file        = "{$installPath}/xbase/{$path}";
            if (!is_file($file)) {
                return response('<h1>404 Not Found</h1>', 404);
            }
            return response()->withFile($file);
        });
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