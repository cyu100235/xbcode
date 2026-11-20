<?php
/**
 * ThinkPHP 缓存前缀覆盖
 *
 * 覆盖配置、会话登记、验证码、登录失败计数、模型字段等全部 Cache:: 用法，
 * 连同 tag: 键一并落入实例命名空间。
 */
use plugin\xbCode\utils\FrameUtil;

return [
    'stores' => [
        'redis' => [
            'prefix' => FrameUtil::cacheNamespace(),
        ],
    ],
];
