<?php
return [
    'default' => [
        'host' => xbEnv('REDIS.HOST', '127.0.0.1'),
        'password' => xbEnv('REDIS.PASSWD', null),
        'port' => xbEnv('REDIS.PORT', 6379),
        'database' => xbEnv('REDIS.DATABASE', 0),
    ],
];       
