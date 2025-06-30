<?php
return [
    'default' => [
        'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
        'password' => getenv('REDIS_PASSWORD') ?: null,
        'port' => getenv('REDIS_PORT') ?: 6379,
        'database' => getenv('REDIS_DB') ?: 0,
        'pool' => [
            'max_connections' => 5,
            'min_connections' => 1,
            'wait_timeout' => 3,
            'idle_timeout' => 60,
            'heartbeat_interval' => 50,
        ],
    ]
];
