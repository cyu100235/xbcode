<?php
declare(strict_types=1);

namespace app\common\middleware;

use Closure;
use think\App;

/**
 * 应用服务中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ServiceMiddleware
{
    /** @var App */
    protected $app;

    /**
     * 中间件构造函数
     * @param \think\App $app
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 应用插件
     * @param mixed $request
     * @param \Closure $next
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function handle($request, Closure $next)
    {
        // 1.初始化应用插件基础参数
        $this->initPlugin();
        // 2.加载插件配置
        $this->loadConfig();
        // 3.加载应用插件composer包
        $this->loadComposer();
        // 4.注册插件中间件
        $this->registerMiddlewares();

        // 调度转发
        return $this->app->middleware
            ->pipeline('plugin')
            ->send($request)
            ->then(function ($request) use ($next) {
                return $next($request);
            });
    }

    /**
     * 初始化插件
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function initPlugin()
    {
        # 获取实例
        $app = $this->app;
        # 获取请求对象
        $request = $app->request;
        # 获取应用名称
        $appName = $request->route('name', '');
        # 插件目录名
        $appDir = 'xbase';
        # 设置插件名称
        $request->xBaseName = $appName;
        # 设置应用总路径
        $request->xBaseRootPath = $this->app->getRootPath()."{$appDir}/";
        # 设置应用目录
        $request->xBasePath = "{$request->xBaseRootPath}/{$appName}/";
        # 设置插件应用目录
        $request->xBaseAppPath = $request->xBaseRootPath . "app/";
        # 设置插件模板目录
        $request->xBaseViewPath = $request->xBaseRootPath . "view/";
        # 设置插件配置文件目录
        $request->xBaseConfigPath = $request->xBaseRootPath . "config/";
        # 设置插件静态资源目录
        $request->xBasePublicPath = $request->xBaseRootPath . "public/";
    }

    /**
     * 加载配置项
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function loadConfig()
    {
        # 请求对象
        $request = $this->app->request;
        # 应用名称
        $appName = $request->xBaseName;
        # 应用根目录
        $rootPath = $request->xBaseRootPath;
        # 应用App目录
        $appPath = $request->xBaseAppPath;
        # 插件配置目录
        $configPath = $request->xBaseConfigPath;
        # 加载TP类型函数库文件
        if (is_file($appPath . '/common.php')) {
            include_once $appPath . '/common.php';
        }
        $files = [];
        # 合并配置文件
        $files = array_merge($files, glob($configPath . '*' . $this->app->getConfigExt()));
        # 加载配置文件
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }
            $configName = pathinfo($file, PATHINFO_FILENAME);
            $configData = include $file;
            if (is_array($configData)) {
                $configs = $this->app->config->get("plugin.{$appName}", []);
                if (empty($configs)) {
                    # 首次添加
                    $configData = [
                        $plugin => [
                            $configName => $configData,
                        ],
                    ];
                } else {
                    # 后续添加
                    $configs[$configName] = $configData;
                    $configData           = [
                        $plugin => $configs,
                    ];
                }
                $this->app->config->set($configData, 'plugin');
            }
        }

        # 加载事件文件
        if (is_file($configPath . '/event.php')) {
            $this->app->loadEvent(include $configPath . '/event.php');
        }
        # 加载容器文件
        if (is_file($configPath . '/provider.php')) {
            $this->app->bind(include $configPath . '/provider.php');
        }
    }

    /**
     * 加载Composer包
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function loadComposer()
    {
        $request    = $this->app->request;
        $pluginPath = $request->pluginPath;

        # 检测插件内composer包
        $packageFile = $pluginPath . "package/vendor/autoload.php";
        if (!is_file($packageFile)) {
            return;
        }
        # 加载插件内composer包
        include_once $packageFile;
    }

    /**
     * 注册插件中间件
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private function registerMiddlewares()
    {
        $request = $this->app->request;
        # 获取框架全局中间件
        $middleware = config('plugin.middleware', []);
        # 获取层级中间件
        $levelMiddleware = $this->app->request->levelRoute;
        # 获取插件应用中间件
        $pluginMiddleware = config("plugin.{$request->plugin}.middleware.{$levelMiddleware}", []);
        # 合并中间件
        $middlewares = array_merge($middleware, $pluginMiddleware);
        # 注册插件全局中间件
        $this->app->middleware->import($middlewares, 'plugin');
    }
}