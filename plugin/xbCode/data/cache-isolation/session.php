<?php
/**
 * 会话前缀覆盖
 *
 * 当前使用 file 驱动（runtime/sessions，本就按环境隔离）；
 * 这里同步隔离 redis / redis_cluster 变体，切换到该驱动时不会串环境。
 */
use plugin\xbCode\utils\FrameUtil;

return [
    'config' => [
        'redis' => [
            'prefix' => 'redis_session_' . FrameUtil::cacheNamespace(),
        ],
        'redis_cluster' => [
            'prefix' => 'redis_session_' . FrameUtil::cacheNamespace(),
        ],
    ],
];
