<?php
return [
    'default' => [
        'host' => xbEnv('REDIS_HOST', '127.0.0.1'),
        'password' => xbEnv('REDIS_PASSWORD', null),
        'port' => xbEnv('REDIS_PORT', 6379),
        'database' => xbEnv('REDIS_DB', 0),
    ],
];
