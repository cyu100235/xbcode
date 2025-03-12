<?php

$pluginPath = base_path().'/plugin';
$data = glob("{$pluginPath}/*/app/admin", GLOB_ONLYDIR);

$middlewares = [];
foreach ($data as $value) {
    $middleware = str_replace(base_path().'/', '', $value);
    $middleware = str_replace('app/', '', $middleware);
    $middleware = str_replace('/', '.', $middleware);
    $middlewares[$middleware] = [
        \plugin\xbCode\app\admin\middleware\AuthMiddleware::class,
    ];
}
$data = glob("{$pluginPath}/xb*", GLOB_ONLYDIR);
foreach ($data as $value) {
    $middleware = str_replace(base_path().'/', '', $value);
    $middleware = str_replace('app/', '', $middleware);
    $middleware = str_replace('/', '.', $middleware);
    $middlewares[$middleware] = [
        \plugin\xbCode\app\middleware\XbMiddleware::class,
    ];
}

return $middlewares;
