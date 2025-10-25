<?php
namespace plugin\xbCode\utils;

/**
 * 中间件工具类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class MiddlewareUtil
{
    /**
     * 中间件前缀
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static $pluginPrefix = 'xb';

    /**
     * 系统中间件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static $middlewares = [
        '@' => [
            \plugin\xbCode\app\middleware\XbMiddleware::class,
        ],
    ];

    /**
     * 扫描所有模块插件中间件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function modulesMiddleware()
    {
        // 插件根目录
        $pluginPath = base_path() . '/plugin';
        // 插件前缀
        $pluginPrefix = self::$pluginPrefix;
        // 扫描模块
        $data = glob("{$pluginPath}/{$pluginPrefix}*/config/middleware.php");
        $middlewares = [];
        foreach ($data as $file) {
            $temp = include $file;
            if (empty($temp)) {
                continue;
            }
            if (!is_array($temp)) {
                continue;
            }
            foreach ($temp as $module => $value) {
                // 跳过其他中间件
                if (str_contains($module, '.')) {
                    continue;
                }
                // 获取中间件
                $middleware = array_unique(array_merge($middlewares[$module] ?? [], $value));
                $middlewares[$module] = $middleware;
            }
        }
        return $middlewares;
    }

    /**
     * 获取所有模块中间件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function modules()
    {
        // 插件根目录
        $pluginPath = base_path() . '/plugin';
        // 插件前缀
        $pluginPrefix = self::$pluginPrefix;
        // 扫描模块
        $data = glob("{$pluginPath}/{$pluginPrefix}*/app/*/controller", GLOB_ONLYDIR);
        // 获取模块中间件
        $moduleMiddleware = static::modulesMiddleware();
        // 获取所有模块中间件
        $middlewares = [];
        foreach ($data as $value) {
            // 获取插件名称
            $pluginName = basename(dirname($value, 3));
            // 获取模块名称
            $module = basename(dirname($value));
            // 获取模块KEY
            $moduleKey = "plugin.{$pluginName}.{$module}";
            // 获取模块中间件
            $middlewares[$moduleKey] = $moduleMiddleware[$module] ?? [];
        }
        // 追加超全局中间件
        $middlewares['@'] = array_unique(array_merge(static::$middlewares['@'], $middlewares['@'] ?? []));
        return $middlewares;
    }
}