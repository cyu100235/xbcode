<?php
/**
 * support\Redis 连接前缀覆盖
 *
 * 按实例端口命名空间化，避免同机多环境（本地 / 测试 / 生产）共用 Redis 时互相读写。
 * phpredis 的 OPT_PREFIX 同样作用于 Lua EVAL 的 KEYS，
 * 因此使用 Lua 脚本的命令无需自己拼接前缀。
 */
use plugin\xbCode\utils\FrameUtil;

return [
    'default' => [
        'prefix' => FrameUtil::cacheNamespace(),
    ],
];
