<?php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST') ?: '127.0.0.1',
            'port'        => getenv('DB_PORT') ?: '3306',
            'database'    => getenv('DB_NAME') ?: '',
            'username'    => getenv('DB_USER') ?: '',
            'password'    => getenv('DB_PASS') ?: '',
            'charset'     => getenv('DB_CHARSET') ?: 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
            'prefix'      => getenv('DB_PREFIX') ?: 'xb_',
            'strict'      => true,
            'engine'      => null,
            'options'   => [
                // 当使用swoole或swow作为驱动时是必须的
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
            'pool' => [
                // 最大连接数
                'max_connections' => 5,
                // 最小连接数
                'min_connections' => 1,
                // 从连接池获取连接等待的最大时间，超时后会抛出异常。仅在协程环境有效
                'wait_timeout' => 3,
                // 连接池中连接最大空闲时间，超时后会关闭回收，直到连接数为min_connections
                'idle_timeout' => 60,
                // 连接池心跳检测时间，单位秒，建议小于60秒
                'heartbeat_interval' => 50,
            ],
        ],
    ],
];