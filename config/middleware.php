<?php

try {
    return [
        '@' => [
            \plugin\xbCode\app\middleware\XbMiddleware::class,
            \plugin\xbCode\app\middleware\PluginMiddleware::class,
        ],
    ];
} catch (\Throwable $th) {
    return [];
}