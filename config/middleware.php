<?php
// 站点根目录
$baseDir = dirname(__DIR__);
// 插件目录
$pluginDir = "{$baseDir}/plugin";
// 应用目录
$appDir = "{$baseDir}/app";

// 中间件数据
$middlewares = [];

// 超全局中间件
$middlewares['@'] = [
    // 跨域中间件
    \xbcode\middleware\CrossMiddleware::class,
    // 静态文件中间件
    \xbcode\middleware\StaticFileMiddleware::class,
    // 安装检测中间件
    \xbcode\middleware\InstallMiddleware::class,
    // 授权服务检查中间件
    \xbcode\middleware\ServerAuthMiddleware::class,
];
// 主项目总后台模块中间件
$middlewares['backend'] = [
    // 授权服务跳转中间件
    \xbcode\middleware\ServerRedirectMiddleware::class,
];
// 主项目子后台模块中间件
$middlewares['admin'] = [
    // 租户站点中间件
    \xbcode\middleware\WebSiteMiddleware::class,
];
// 主项目默认模块中间件
$middlewares['index'] = [
    // 租户站点中间件
    \xbcode\middleware\WebSiteMiddleware::class,
];

// 扫描全局中间件
$rootMiddlewareDir = $appDir . '/middleware';
if (is_dir($rootMiddlewareDir)) {
    foreach (glob($rootMiddlewareDir . '/*.php') as $file) {
        $middlewares[''][] = 'app\\middleware\\' . pathinfo($file, PATHINFO_FILENAME);
    }
}

// 扫描模块中间件
foreach (glob($appDir . '/*', GLOB_ONLYDIR) as $moduleDir) {
    $moduleName = basename($moduleDir);
    $middlewareDir = $moduleDir . '/middleware';

    if (is_dir($middlewareDir)) {
        foreach (glob($middlewareDir . '/*.php') as $file) {
            $middlewares[$moduleName][] = 'app\\' . $moduleName . '\\middleware\\' . pathinfo($file, PATHINFO_FILENAME);
        }
    }
}

// 扫描插件中间件
if (is_dir($pluginDir)) {
    // 扫描插件目录路径
    $pluginDirPath = glob($pluginDir . '/*', GLOB_ONLYDIR);
    // 获取插件名称
    $pluginNames = array_map(function ($item) {
        return basename($item);
    }, $pluginDirPath);
    foreach ($pluginNames as $value) {
        $middlewares["plugin.{$value}"] = [
            // 插件中间件
            \xbcode\middleware\PluginMiddleware::class,
            // 租户站点中间件
            \xbcode\middleware\WebSiteMiddleware::class,
        ];
    }
}

// 返回中间件数据
return $middlewares;