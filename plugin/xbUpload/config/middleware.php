<?php

// 后台鉴权中间件
return [
    'admin' => [
        \plugin\xbCode\app\admin\middleware\AuthMiddleware::class,
    ],
];
