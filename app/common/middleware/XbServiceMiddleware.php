<?php
declare(strict_types=1);

namespace app\common\middleware;

use app\common\model\Projects;
use app\common\service\cloud\AppCloud;
use Closure;
use Exception;
use think\App;
use think\facade\Cache;
use think\Request;
use think\Response;

/**
 * 系统服务中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbServiceMiddleware
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
     * @param \think\Request $request
     * @param \Closure $next
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function handle(Request $request, Closure $next)
    {
        // 1.初始化应用插件基础参数
        $this->initPlugin();
        // 2.加载插件配置
        $this->loadConfig();
        // 3.加载应用插件composer包
        $this->loadComposer();
        // 4.注册插件中间件
        $this->registerMiddlewares();
        // 5.检测是否静态文件
        if ($staticContent = $this->checkStaticFile($request)) {
            return $staticContent;
        }
        // 调度转发
        return $this
            ->app
            ->middleware
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
        // 获取实例
        $app = $this->app;
        // 获取请求对象
        $request = $app->request;
        // 访问应用名称
        $httpName = $request->route('name', '');
        // 应用模块名称
        $moduleName = $request->route('module', '');
        // 应用控制器名称
        $controlName = $request->route('control', 'Index');
        // 应用操作名称
        $actionName = $request->route('action', 'index');
        // 获取项目数据
        $project = self::getAppInfo($httpName);
        // 插件目录名
        $appDir = 'base';
        // 应用ID
        $request->saas_appid = $project->id;
        // 项目名称
        $request->app_title = $project->title;
        // 项目图标
        $request->app_logo = $project->logo;
        // 设置应用名称
        $request->appName = $project->app_name;
        // 设置访问应用名称
        $request->xBaseName = $project->name;
        // 设置应用模块名称
        $request->xModuleName = $moduleName;
        // 设置应用控制器名称
        $request->setController($controlName);
        // 设置应用操作名称
        $request->setAction($actionName);
        // 设置应用模块命名空间
        $this->app->setNamespace("{$appDir}\\{$request->appName}\\app");
        // 设置应用目录
        $request->xBaseDir = $appDir;
        // 设置应用总路径
        $request->xBaseRootPath = $this->app->getRootPath() . "{$appDir}/";
        // 设置应用目录
        $request->xBasePath = $request->xBaseRootPath . "{$request->appName}/";
        // 设置插件应用目录
        $request->xBaseAppPath = $request->xBasePath . "app/";
        // 设置应用模块目录
        $request->xBaseModulePath = $request->xBaseAppPath . "{$moduleName}/";
        // 应用中间件
        $request->xBaseMiddleware = $request->xBaseModulePath . "middleware/";
        // 设置插件模板目录
        $request->xBaseViewPath = $request->xBaseModulePath . "view/";
        // 设置插件配置文件目录
        $request->xBaseConfigPath = $request->xBasePath . "config/";
        // 设置插件静态资源目录
        $request->xBasePublicPath = $request->xBasePath . "public/";
    }

    /**
     * 获取应用数据
     * @param string $projectName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getAppInfo(string $projectName)
    {
        $project = Cache::get($projectName, '');
        if ($project) {
            return $project;
        }
        // 查询项目数据
        $project = Projects::where('name', $projectName)->find();
        if (!$project) {
            throw new Exception("应用不存在：{$projectName}");
        }
        // 设置缓存5分钟
        Cache::set($projectName, $project, 300);
        return $project;
    }

    /**
     * 加载配置项
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function loadConfig()
    {
        // 请求对象
        $request = $this->app->request;
        // 应用名称
        $appName = $request->appName;
        // 应用根目录
        $rootPath = $request->xBaseRootPath;
        // 应用App目录
        $appPath = $request->xBaseAppPath;
        // 插件配置目录
        $configPath = $request->xBaseConfigPath;
        // 加载TP类型函数库文件
        if (is_file($appPath . '/common.php')) {
            include_once $appPath . '/common.php';
        }
        $files = [];
        // 合并配置文件
        $files = array_merge($files, glob($configPath . '*' . $this->app->getConfigExt()));
        // 加载配置文件
        foreach ($files as $file) {
            if (!is_file($file)) {
                continue;
            }
            $configName = pathinfo($file, PATHINFO_FILENAME);
            $configData = include $file;
            if (is_array($configData)) {
                $configs = $this->app->config->get("plugin.{$appName}", []);
                if (empty($configs)) {
                    // 首次添加
                    $configData = [
                        $appName => [
                            $configName => $configData,
                        ],
                    ];
                } else {
                    // 后续添加
                    $configs[$configName] = $configData;
                    $configData           = [
                        $appName => $configs,
                    ];
                }
                $this->app->config->set($configData, 'plugin');
            }
        }

        // 加载事件文件
        if (is_file($configPath . '/event.php')) {
            $this->app->loadEvent(include $configPath . '/event.php');
        }
        // 加载容器文件
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

        // 检测插件内composer包
        $packageFile = $pluginPath . "package/vendor/autoload.php";
        if (!is_file($packageFile)) {
            return;
        }
        // 加载插件内composer包
        include_once $packageFile;
    }

    /**
     * 注册应用中间件
     * @return void
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     * @email 416716328@qq.com
     */
    private function registerMiddlewares()
    {
        // 获取请求对象
        $request = $this->app->request;
        // 扫描中间件
        $middlewares = glob($request->xBaseMiddleware . '*.php');
        // 处理中间件获取命名空间
        $middlewares = array_map(function ($middleware) {
            $middleware = str_replace($this->app->getRootPath(), '', $middleware);
            $middleware = str_replace('/', "\\", $middleware);
            return str_replace('.php', '', $middleware);
        }, $middlewares);
        // 注册插件全局中间件
        $this->app->middleware->import($middlewares, 'plugin');
    }
    
    /**
     * 检测是否静态文件
     * @param \think\Request $request
     * @return Response|bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function checkStaticFile(Request $request)
    {
        // 检测是否默认访问文件
        $fileType = self::getFileRule();
        $path       = "base/{$request->appName}";
        $staticFile = root_path()."{$path}/public/index.html";
        if (file_exists($staticFile)) {
            $content = file_get_contents($staticFile);
            return Response::create($content);
        }
        // 检测是否静态文件
        $path   = $request->pathinfo();
        if (preg_match($fileType['rule'], $path)) {
            $path   = str_replace("base/{$request->xBaseName}/", '', $path);
            $path   = $request->xBasePublicPath . $path;
            // 检测文件是否存在
            if (file_exists($path)) {
                // 文件类型
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                // 获取文件类型
                $contentType = $fileType['mimeContentTypes'][$ext] ?? 'text/plain';
                $content = file_get_contents($path);
                // 返回文件内容
                return Response::create($content)->contentType($contentType);
            }
        }
        // 未找到文件
        return false;
    }

    /**
     * 文件类型
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function getFileRule()
    {
        // 检测文件规则
        $rule = '/\.(?:png|jpg|jpeg|gif|ico|js|vue|html|css|eot|svg|ttf|woff|woff2|otf|map)$/i';
        // 文件类型
        $mimeContentTypes = [
            'xml'   => 'application/xml,text/xml,application/x-xml',
            'json'  => 'application/json,text/x-json,application/jsonrequest,text/json',
            'js'    => 'text/javascript,application/javascript,application/x-javascript',
            'css'   => 'text/css',
            'rss'   => 'application/rss+xml',
            'yaml'  => 'application/x-yaml,text/yaml',
            'atom'  => 'application/atom+xml',
            'pdf'   => 'application/pdf',
            'text'  => 'text/plain',
            'image' => 'image/png,image/jpg,image/jpeg,image/pjpeg,image/gif,image/webp,image/*',
            'csv'   => 'text/csv',
            'html'  => 'text/html,application/xhtml+xml,*/*',
            'vue'   => 'application/octet-stream',
            'svg'   => 'image/svg+xml',
        ];
        // 返回规则数据
        return [
            'rule'              => $rule,
            'mimeContentTypes'  => $mimeContentTypes,
        ];
    }
}