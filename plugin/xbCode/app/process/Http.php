<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\app\process;

use ArrayObject;
use Throwable;
use Webman\App;
use Webman\Context;
use Webman\Http\Request;
use Webman\Util;

/**
 * HTTP进程处理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Http extends App
{
    /**
     * 处理 HTTP 消息
     * @param mixed $connection
     * @param mixed $request
     * @return null
     * @throws Throwable
     */
    public function onMessage($connection, $request)
    {
        try {
            Context::reset(new ArrayObject([Request::class => $request]));
            $plugin = static::pluginName();
            $path = $request->path();
            $key = $request->method() . $path;

            // 命中路由缓存，直接执行
            if (isset(static::$callbacks[$key])) {
                [$callback, $request->plugin, $request->app, $request->controller, $request->action, $request->route] = static::$callbacks[$key];
                static::send($connection, $callback($request), $request);
                return null;
            }

            // 非法 URI 拦截、静态文件直出（复用上级能力）
            if (static::unsafeUri($connection, $path, $request) || static::findFile($connection, $path, $key, $request)) {
                return null;
            }

            // 使用插件内部路由解析控制器与方法，未命中时回落到默认首页 Index::index
            $controllerAndAction = static::parseControllerAction($path) ?: static::parseControllerAction("/$plugin");
            if (!$controllerAndAction) {
                $request->plugin = $plugin;
                $request->app = $request->controller = $request->action = '';
                $callback = static::getFallback($plugin);
                static::send($connection, $callback($request), $request);
                return null;
            }

            $plugin = $controllerAndAction['plugin'] ?: $plugin;
            $app = $controllerAndAction['app'];
            $controller = $controllerAndAction['controller'];
            $action = $controllerAndAction['action'];

            $callback = static::getCallback($plugin, $app, [$controller, $action]);
            static::collectCallbacks($key, [$callback, $plugin, $app, $controller, $action, null]);
            [$callback, $request->plugin, $request->app, $request->controller, $request->action, $request->route] = static::$callbacks[$key];
            static::send($connection, $callback($request), $request);
        } catch (Throwable $e) {
            static::send($connection, static::exceptionResponse($e, $request), $request);
        }
        return null;
    }

    /**
     * 进程启动回调
     * @param mixed $worker
     * @return void
     */
    public function onWorkerStart($worker)
    {
        // 静态资源根目录指向插件自身 public：BASE_PATH/plugin/{插件}/public
        static::$publicPath = public_path('', static::pluginName());
        parent::onWorkerStart($worker);
    }

    /**
     * 解析控制器与方法（插件内部路由）
     * @param string $path
     * @return array|false
     * @throws Throwable
     */
    protected static function parseControllerAction(string $path)
    {
        $path = str_replace(['-', '//'], ['', '/'], $path);
        if (static::containsPathTraversal($path)) {
            return false;
        }

        static $cache = [];
        if (isset($cache[$path])) {
            return $cache[$path];
        }

        // 首段为插件名，未命中已安装插件时按当前进程所属插件解析
        $plugin = static::pluginName();
        $segments = static::pathSegments($path);
        if ($segments && ($name = static::findPlugin($segments[0]))) {
            $plugin = $name;
            array_shift($segments);
        }

        $suffix = (string) static::config($plugin, 'app.controller_suffix', '');
        // 命名空间前缀由解析出的插件名决定
        $classPrefix = "plugin\\$plugin";
        foreach (static::routeCandidates($segments) as [$app, $controller, $action]) {
            if (!static::isValidSegment($action)) {
                continue;
            }
            $controllerClass = static::controllerClass($classPrefix, $app, $controller, $suffix);
            if ($controllerClass && ($controllerAction = static::getControllerAction($controllerClass, $action))) {
                if (!isset($path[256])) {
                    $cache[$path] = $controllerAction;
                    if (count($cache) > 1024) {
                        unset($cache[key($cache)]);
                    }
                }
                return $controllerAction;
            }
        }
        return false;
    }

    /**
     * 生成路由候选（应用优先，无同名应用时退化为控制器）
     * @param array $segments
     * @return array
     */
    protected static function routeCandidates(array $segments): array
    {
        if (!$segments) {
            return [['', 'index', 'index']];
        }
        $count = count($segments);
        $candidates = [
            // /插件名  或  /插件名/应用名  或 /插件名/应用名/控制器名  或 /插件名/应用名/控制器名/方法名
            [$segments[0], $segments[1] ?? 'index', $segments[2] ?? 'index'],
            // /插件名/应用名/方法名
            [$segments[0], 'index', $segments[1] ?? 'index'],
            // /插件名/控制器名  或  /插件名/控制器名/方法名
            ['', $segments[0], $segments[1] ?? 'index'],
            // /插件名/多级控制器名/方法名
            ['', implode('/', array_slice($segments, 0, $count - 1)), $segments[$count - 1]],
            // /插件名/多级控制器名
            ['', implode('/', $segments), 'index'],
        ];
        $map = [];
        foreach ($candidates as [$app, $controller, $action]) {
            if ($controller === '' || isset($map["$app|$controller|$action"])) {
                continue;
            }
            $map["$app|$controller|$action"] = [$app, $controller, $action];
        }
        return array_values($map);
    }

    /**
     * 组装控制器类名
     * @param string $classPrefix
     * @param string $app
     * @param string $controller
     * @param string $suffix
     * @return string|false
     */
    protected static function controllerClass(string $classPrefix, string $app, string $controller, string $suffix): string|false
    {
        $controllerSegments = explode('/', strtolower($controller));
        foreach ($controllerSegments as $segment) {
            if (!static::isValidSegment($segment)) {
                return false;
            }
        }
        $parts = [$classPrefix, 'app'];
        if ($app !== '') {
            $app = strtolower($app);
            if (!static::isValidSegment($app)) {
                return false;
            }
            $parts[] = $app;
        }
        $parts[] = 'controller';
        foreach ($controllerSegments as $segment) {
            $parts[] = ucfirst($segment);
        }
        return implode('\\', $parts) . $suffix;
    }

    /**
     * 拆分访问路径
     * @param string $path
     * @return array
     */
    protected static function pathSegments(string $path): array
    {
        $relative = trim($path, '/');
        return $relative === '' ? [] : explode('/', $relative);
    }

    /**
     * 匹配已安装的插件名（忽略大小写）
     * @param string $name
     * @return string
     */
    protected static function findPlugin(string $name): string
    {
        static $plugins = null;
        if ($plugins === null) {
            $plugins = [];
            foreach (Util::scanDir(base_path() . '/plugin', false) as $dir) {
                $plugins[strtolower($dir)] = $dir;
            }
        }
        return $plugins[strtolower($name)] ?? '';
    }

    /**
     * 判断路由段是否为合法的类名或方法名
     * @param string $segment
     * @return bool
     */
    protected static function isValidSegment(string $segment): bool
    {
        return (bool) preg_match('/^[a-z_][a-z0-9_]*$/i', $segment);
    }

    /**
     * 当前进程所属插件名
     * @return string
     */
    protected static function pluginName(): string
    {
        return static::getPluginByClass(get_called_class()) ?: 'xbCode';
    }
}
