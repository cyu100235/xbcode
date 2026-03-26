<?php

$redisHost = env('REDIS_HOST', '127.0.0.1');
$redisPassword = env('REDIS_PASSWORD');
$redisPort = env('REDIS_PORT', 6379);
$redisDb = env('REDIS_DB', 0);
$redisPrefix = env('REDIS_PREFIX', '');

return [
    'default' => [
        // 服务器地址
        'host' => "redis://{$redisHost}:{$redisPort}",
        // 连接选项
        'options' => [
            // 密码，字符串类型，可选参数
            'auth' => $redisPassword,
            // 数据库
            'db' => $redisDb,
            // 前缀
            'prefix' => $redisPrefix,
            // 消费失败后，重试次数
            'max_attempts' => 5,
            // 重试间隔，单位秒
            'retry_seconds' => 5,
        ],
    ],
];
