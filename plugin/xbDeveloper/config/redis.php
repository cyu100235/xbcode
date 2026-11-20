<?php

use plugin\xbCode\utils\FrameUtil;
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
        // 按实例端口隔离，避免同机多环境共用 Redis 时互相读写
        'prefix' => FrameUtil::cacheNamespace(),
    ],
];
