<?php

return [
    'admin' => [
        \plugin\xbCode\app\admin\middleware\AuthMiddleware::class,
    ],
    '@' => [
        \plugin\xbCode\app\middleware\CorsMiddleware::class,
    ],
];
