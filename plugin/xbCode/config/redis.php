<?php
use plugin\xbCode\utils\FrameUtil;

return [
    'default' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DB', 0),
        // 按实例端口隔离，避免同机多环境共用 Redis 时互相读写
        'prefix' => FrameUtil::cacheNamespace(),
        'pool' => [
            'max_connections' => 5,
            'min_connections' => 1,
            'wait_timeout' => 3,
            'idle_timeout' => 60,
            'heartbeat_interval' => 50,
        ],
    ]
];
