<?php

$middlewares = [
    'admin' => [
        \plugin\xbCode\app\admin\middleware\AuthMiddleware::class,
    ],
];

if (class_exists('\plugin\xbUser\app\api\middleware\AuthMiddleware')) {
    $middlewares['api'] = [
        \plugin\xbUser\app\api\middleware\AuthMiddleware::class
    ];
}

return $middlewares;