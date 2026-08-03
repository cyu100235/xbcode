<?php

return [
    // 后台鉴权中间件
    'admin' => [
        \plugin\xbCode\app\admin\middleware\AuthMiddleware::class,
    ],
];