<?php

return [
    '@' => [
        \plugin\xbCode\app\middleware\XbMiddleware::class,
        \plugin\xbCode\app\middleware\PluginMiddleware::class,
    ],
    'admin' => [
        \plugin\xbCode\app\admin\middleware\AuthMiddleware::class,
    ],
];
