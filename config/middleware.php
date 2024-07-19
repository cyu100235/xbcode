<?php

return array_merge(
    [
        'admin' => [
            \app\admin\middleware\AuthMiddleware::class,
        ],
    ],
    \app\common\providers\RouteProvider::pluginMiddleware(),
);