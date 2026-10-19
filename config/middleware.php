<?php

try {
    return [
        '@' => [
            \plugin\xbCode\app\middleware\XbMiddleware::class,
            \plugin\xbCode\app\middleware\PluginMiddleware::class,
            \plugin\xbCode\app\admin\middleware\AdminLogMiddleware::class,
        ],
    ];
} catch (\Throwable $th) {
    return [];
}