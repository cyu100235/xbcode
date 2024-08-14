<?php
namespace app\common\providers;

use app\common\middleware\PluginsMiddleware;
use app\admin\middleware\AuthMiddleware;
use app\common\service\CloudSerivce;
use app\common\utils\InstallUtil;
use app\common\utils\ZipUtil;
use yzh52521\EasyHttp\Http;
use app\model\AdminRule;
use think\facade\Cache;
use support\Request;
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
    public static function regBaseRoute()
    {
        // 注册静态文件路由
        Route::get('/install/assets/[{path:.+}]', function (Request $request, $path = '') {
            $project = 'install-view';
            $file    = "assets/{$path}";
            $path    = base_path("runtime/{$project}/{$file}");
            if (!file_exists($path)) {
                return response('<h1>local file 404 Not Found</h1>', 404);
            }
            return response()->withFile($path);
        });
        // 检测路由类型
        if (!InstallUtil::hasInstall()) {
            // 安装进行
            Route::post('/install/install', 'app\controller\InstallController@install');
        } else {
            // 注册后台路由
            self::registerAdminView();
            // 注册设置路由
            self::regSettingsRouter();
            // 注册文档路由
            self::regApidocView();
        }
    }

    /**
     * 注册设置路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function regSettingsRouter()
    {
        // 控制器
        $control = \app\admin\controller\SettingsController::class;
        $methods = ['GET', 'PUT'];
        // 配置路由
        Route::add($methods, '/app/{plugin}/Settings/config[/{group}]', [$control, 'config']);
        // 选中配置路由
        Route::add($methods, '/app/{plugin}/Settings/selected[/{group}]', [$control, 'selected']);
        // tabs配置路由
        Route::add($methods, '/app/{plugin}/Settings/tabs[/{group}]', [$control, 'tabs']);
    }

    /**
     * 注册后台视图路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function registerAdminView()
    {
        // 模块名称
        $moduleName = config('admin.module_name', '');
        if (!$moduleName) {
            throw new Exception('请配置后台模块名称');
        }
        $project = 'admin-view';
        // 注册静态文件路由
        Route::get('/xbase/[{path:.+}]', function (Request $request, $path = '') use ($project) {
            $file = "xbase/{$path}";
            $path = base_path("runtime/{$project}/{$file}");
            if (!file_exists($path)) {
                return response('<h1>local file 404 Not Found</h1>', 404);
            }
            return response()->withFile($path);
        });
    }

    /**
     * 注册文档视图路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function regApidocView()
    {
        $project = 'apidoc-view';
        // 注册静态文件路由
        $data = [
            'config.js',
            'style.css',
            'favicon.ico',
            'utils/md5.js',
        ];
        foreach ($data as $file) {
            Route::get("/apidoc/{$file}", function (Request $request, $path = '') use ($project, $file) {
                $path = base_path("runtime/{$project}/{$file}");
                if (!file_exists($path)) {
                    return response('<h1>local file 404 Not Found</h1>', 404);
                }
                return response()->withFile($path);
            });
        }
        // 注册其他静态资源
        Route::get('/apidoc/assets/[{path:.+}]', function (Request $request, $path = '') use ($project) {
            $file = "assets/{$path}";
            $path = base_path("runtime/{$project}/{$file}");
            if (!file_exists($path)) {
                return response('<h1>local file 404 Not Found</h1>', 404);
            }
            return response()->withFile($path);
        });
    }

    /**
     * 下载视图文件
     * @param string $project
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function downloadView(string $project)
    {
        $filename = "{$project}.zip";
        $dirPath  = base_path("runtime/{$project}");
        $filePath = "{$dirPath}/{$filename}";
        if (!file_exists("{$dirPath}/index.html")) {
            // 创建目录
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0777, true);
            }
            // 下载文件
            $baseUrl = "http://view.xiaobai.host/{$filename}";
            $result  = Http::get($baseUrl);
            $content = $result->body();
            if (!$content) {
                return false;
            }
            // 写入文件
            file_put_contents($filePath, $content);
            // 解压文件
            ZipUtil::unzip($filePath, $dirPath);
            // 删除压缩包
            unlink($filePath);
        }
        return true;
    }

    /**
     * 注册插件路由
     * @param string $name 插件名称
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function regPluginRoute(string $name)
    {
        $data = Cache::get('admin_menus', []);
        $data = array_filter($data, function ($item) use ($name) {
            return $name === $item['plugin_name'] && strrpos($item['path'], '/') !== false;
        });
        $data = array_values($data);
        // 获取控制器后缀
        $suffix = config('route.controller_suffix', 'Controller');
        foreach ($data as $value) {
            $module_name = '';
            $moduleName  = '';
            $path        = $value['path'];
            // 配置项路由
            if (strrpos($path, 'Settings/') !== false) {
                continue;
            }
            // if (self::registerPluginConfigRoute($value)) {
            //     continue;
            // }
            $class = explode('/', $path);
            if (count($class) < 1) {
                throw new Exception('路由配置错误');
            }
            $classPath = "{$class[0]}{$suffix}@{$class[1]}";
            if ($value['module_name']) {
                $module_name = "{$value['module_name']}/";
                $moduleName  = "{$value['module_name']}\\";
            }
            $path  = "/app/{$value['plugin_name']}/{$module_name}{$value['path']}";
            $class = "\\plugin\\{$value['plugin_name']}\\app\\{$moduleName}controller\\{$classPath}";
            Route::add(
                $value['methods'],
                $path,
                $class
            );
        }
    }
    private static function registerPluginConfigRoute(array $data)
    {
        // 配置项控制器
        $configControl = \app\admin\controller\SettingsController::class;
        $path          = $data['path'] ?? '';
        $group         = basename($path);
        $path          = str_replace("/{$group}", '', $path);
        $methods       = $data['methods'];
        if (!is_array($data['methods'])) {
            $methods = explode(',', $data['methods']);
        }
        // 路由地址
        $routePath = "/app/{$data['plugin_name']}/{$path}/{group}";
        // config配置项路由
        if (strrpos($path, 'Settings/config') !== false) {
            Route::add($methods, $routePath, [$configControl, 'config']);
            return true;
        }
        // selected配置项路由
        if (strrpos($path, 'Settings/selected') !== false) {
            Route::add($methods, $routePath, [$configControl, 'selected']);
            return true;
        }
        // tabs配置项路由
        if (strrpos($path, 'Settings/tabs') !== false) {
            Route::add($methods, $routePath, [$configControl, 'tabs']);
            return true;
        }
        return false;
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
        $data    = [];
        foreach ($plugins as $value) {
            // 插件中间件
            $data["plugin.{$value['name']}"] = [
                PluginsMiddleware::class
            ];
            // 插件后台中间件
            $data["plugin.{$value['name']}.admin"] = [
                AuthMiddleware::class
            ];
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