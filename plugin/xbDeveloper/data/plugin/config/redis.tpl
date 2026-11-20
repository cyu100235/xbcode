<?php

use plugin\xbCode\utils\FrameUtil;
return [
    'default' => [
        'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
        'password' => getenv('REDIS_PASSWORD') ?: null,
        'port' => getenv('REDIS_PORT') ?: 6379,
        'database' => getenv('REDIS_DB') ?: 0,
        // 按实例端口隔离，避免同机多环境共用 Redis 时互相读写
        'prefix' => FrameUtil::cacheNamespace(),
    ],
];
