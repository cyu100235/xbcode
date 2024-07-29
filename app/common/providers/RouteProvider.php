<?php
namespace app\common\providers;

use app\common\middleware\PluginsMiddleware;
use app\common\service\CloudSerivce;
use app\common\utils\InstallUtil;
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
        // 注册安装路由
        self::registerInstallView();
        // 检测路由类型
        if (!InstallUtil::hasInstall()) {
            // 安装进行
            Route::post('/install/install', 'app\controller\InstallController@install');
        } else {
            // 是否调试模式
            if (config('app.debug', false)) {
                // 注册文档路由
                Route::get("/apidoc", function () {
                    return redirect('/apidoc/');
                });
                Route::get("/apidoc/", 'app\controller\IndexController@apidoc');
            }
            // 注册后台路由
            self::registerAdminView();
        }
    }

    /**
     * 注册安装视图路由
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function registerInstallView()
    {
        $installPath = base_path('runtime/install-view/index.html');
        $project = 'install-view';
        if (!file_exists($installPath)) {
            $file = 'index.html';
            self::downloadFile($project, $file);
        }
        // 注册文件路由
        Route::get('/install/assets/[{path:.+}]', function (Request $request, $path = '') use ($project) {
            $file = "assets/{$path}";
            if (!self::downloadFile($project, $file)) {
                return response('<h1>remote file 404 Not Found</h1>', 404);
            }
            $path = base_path("runtime/{$project}/{$file}");
            return response()->withFile($path);
        });
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
        // 注册后台路由
        Route::get("/{$moduleName}", function () use ($moduleName) {
            return redirect("/{$moduleName}/");
        });
        Route::get("/{$moduleName}/", 'app\admin\controller\IndexController@index');
        // 注册后台资源路由
        $project = 'admin-view';
        // 注册静态文件路由
        Route::get('/xbase/[{path:.+}]', function (Request $request, $path = '') use ($project) {
            $file = "xbase/{$path}";
            if (!self::downloadFile($project, $file)) {
                return response('<h1>remote file 404 Not Found</h1>', 404);
            }
            $path = base_path("runtime/{$project}/{$file}");
            return response()->withFile($path);
        });
    }

    /**
     * 下载文件
     * @param string $project
     * @param string $path
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function downloadFile(string $project, string $path)
    {
        $runPath = base_path("runtime/{$project}/{$path}");
        // 判断文件是否存在
        if (!file_exists($runPath)) {
            $baseUrl = "http://view.xiaobai.host/{$project}";
            $url = "{$baseUrl}/{$path}";
            $result = Http::get($url);
            $content = $result->body();
            if (!$content) {
                return false;
            }
            $dirPath = dirname($runPath);
            if (!is_dir($dirPath)) {
                mkdir($dirPath, 0755, true);
            }
            file_put_contents($runPath, $content);
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
            $keyword = "plugin\\{$name}\\";
            return strrpos($item['class'], $keyword) !== false;
        });
        $data = array_values($data);
        foreach ($data as $value) {
            if ($value['class'] && $value['path'] && $value['methods']) {
                Route::add(
                    $value['methods'],
                    "/{$value['path']}",
                    $value['class']
                );
            }
        }
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
                    PluginsMiddleware::class
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