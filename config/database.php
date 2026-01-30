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
                PDO::ATTR_EMULATE_PREPARES => false, // Must be false for Swoole and Swow drivers.
            ],
            'pool' => [
                'max_connections' => 5,
                'min_connections' => 1,
                'wait_timeout' => 3,
                'idle_timeout' => 60,
                'heartbeat_interval' => 50,
            ],
        ],
    ],
];