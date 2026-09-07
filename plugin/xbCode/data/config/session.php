<?php
/**
 * 由 xbCode 安装向导生成：Session 配置
 * 会话存储类型跟随 CACHE_TYPE：默认 file，redis / redis_cluster 时会话自动切到对应实现；
 * Redis 相关连接参数与 config/redis.php 同套，全部通过 plugin\xbCode\api\Env 读取，
 * 支持运行期 set/put 动态覆盖 & 按前缀分组读取。
 *
 * @copyright 贵州积木云网络科技有限公司
 */

use plugin\xbCode\api\Env;
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

$type = strtolower((string)Env::get('CACHE_TYPE', 'file'));
if (!in_array($type, ['file', 'redis', 'redis_cluster'], true)) {
    $type = 'file';
}
$handler = match ($type) {
    'redis'         => RedisSessionHandler::class,
    'redis_cluster' => RedisClusterSessionHandler::class,
    default         => FileSessionHandler::class,
};
$savePath = function_exists('runtime_path') ? runtime_path() . '/sessions' : (__DIR__ . '/../runtime/sessions');

return [
    'type'    => $type, // file / redis / redis_cluster
    'handler' => $handler,
    'config'  => [
        'file' => [
            'save_path' => $savePath,
        ],
        'redis' => [
            'host'     => (string)Env::get('REDIS_HOST', '127.0.0.1'),
            'port'     => (int)Env::get('REDIS_PORT', 6379),
            'auth'     => (string)Env::get('REDIS_PASSWORD', ''),
            'timeout'  => (int)Env::get('SESSION_TIMEOUT', 2),
            'database' => (string)Env::get('REDIS_DB', 0),
            'prefix'   => (string)Env::get('SESSION_PREFIX', 'redis_session_'),
        ],
        'redis_cluster' => [
            'host' => array_map('trim', explode(',', (string)Env::get('REDIS_CLUSTER_HOSTS', '127.0.0.1:7000,127.0.0.1:7001,127.0.0.1:7002'))),
            'timeout'  => (int)Env::get('SESSION_TIMEOUT', 2),
            'auth'     => (string)Env::get('REDIS_PASSWORD', ''),
            'prefix'   => (string)Env::get('SESSION_PREFIX', 'redis_session_'),
        ],
    ],
    'session_name'          => (string)Env::get('SESSION_NAME', 'PHPSID'),
    'auto_update_timestamp' => (bool)Env::get('SESSION_AUTO_UPDATE_TS', false),
    'lifetime'              => (int)Env::get('SESSION_LIFETIME', 7 * 24 * 60 * 60),
    'cookie_lifetime'       => (int)Env::get('SESSION_COOKIE_LIFETIME', 365 * 24 * 60 * 60),
    'cookie_path'           => (string)Env::get('SESSION_COOKIE_PATH', '/'),
    'domain'                => (string)Env::get('SESSION_COOKIE_DOMAIN', ''),
    'http_only'             => (bool)Env::get('SESSION_HTTP_ONLY', true),
    'secure'                => (bool)Env::get('SESSION_SECURE', false),
    'same_site'             => (string)Env::get('SESSION_SAME_SITE', ''),
    'gc_probability'        => [
        (int)Env::get('SESSION_GC_NUM', 1),
        (int)Env::get('SESSION_GC_DEN', 1000),
    ],
];