<?php
/**
 * redis-queue 队列名前缀覆盖
 *
 * - options.prefix：队列键（真实生效项），按实例端口隔离后，
 *   测试环境不会再消费到生产环境投递的任务；
 * - 顶层 prefix：该配置同时会被框架合并成 redis.plugin.webman.redis-queue.default
 *   连接（support\Redis::connection 可用），补上前缀以免该连接绕过隔离。
 * host / db / auth 等其余字段由框架原配置保留。
 */
use plugin\xbCode\utils\FrameUtil;

return [
    'default' => [
        'prefix' => FrameUtil::cacheNamespace(),
        'options' => [
            'prefix' => FrameUtil::cacheNamespace(),
        ],
    ],
];
