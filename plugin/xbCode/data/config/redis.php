<?php
/**
 * 由 xbCode 安装向导生成：Redis 配置
 * 所有配置通过 plugin\xbCode\api\Env 读取项目根目录 .env，支持运行期 set/put 动态覆盖
 * 以及按前缀分组读取，读取时会按 .env 字面量自动解析成 int/bool/float/null/string。
 *
 * @copyright 贵州积木云网络科技有限公司
 */

use plugin\xbCode\api\Env;

return [
    'default' => [
        'host'     => (string)Env::get('REDIS_HOST', '127.0.0.1'),
        'port'     => (int)Env::get('REDIS_PORT', 6379),
        'password' => (string)Env::get('REDIS_PASSWORD', ''),
        'database' => (int)Env::get('REDIS_DB', 0),
        'prefix'   => (string)Env::get('REDIS_PREFIX', ''),
        'pool'     => [
            'max_connections'  => (int)Env::get('REDIS_POOL_MAX', 5),
            'min_connections'  => (int)Env::get('REDIS_POOL_MIN', 1),
            'wait_timeout'     => (int)Env::get('REDIS_POOL_WAIT', 3),
            'idle_timeout'     => (int)Env::get('REDIS_POOL_IDLE', 60),
            'heartbeat_interval' => (int)Env::get('REDIS_POOL_HB', 50),
        ],
    ],
];