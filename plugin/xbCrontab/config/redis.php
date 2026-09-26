<?php

return [
    'default' => [
        'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
        'password' => getenv('REDIS_PASSWORD') ?: null,
        'port' => getenv('REDIS_PORT') ?: 6379,
        'database' => getenv('REDIS_DB') ?: 0,
    ],
];
