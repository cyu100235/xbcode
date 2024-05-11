<?php
return [
    'default' => xbEnv('CACHE.TYPE', 'file'),
    'stores' => [
        'file' => [
            'type' => 'File',
            // 缓存保存目录
            'path' => runtime_path() . '/cache/',
            // 缓存前缀
            'prefix' => '',
            // 缓存有效期 0表示永久缓存
            'expire' => 0,
        ],
        'redis' => [
            'type' => 'redis',
            'host' => xbEnv('REDIS.HOST', '127.0.0.1'),
            'port' => xbEnv('REDIS.PORT', 6379),
            'prefix' => xbEnv('REDIS.PREFIX', 'xb_'),
            'expire' => xbEnv('REDIS.EXPIRE_TIME', 0),
        ],
    ],
];