<?php
/**
 * 由 xbCode 安装向导生成：Illuminate Database 配置
 * 所有配置通过 plugin\xbCode\api\Env 读取项目根目录 .env，
 * 支持运行期 set/put 动态覆盖 & 按前缀分组读取；类型按 .env 字面量自动解析。
 *
 * @copyright 贵州积木云网络科技有限公司
 */

use plugin\xbCode\api\Env;

return [
    'default'     => (string)Env::get('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            'driver'      => (string)Env::get('DB_DRIVER', 'mysql'),
            'host'        => (string)Env::get('DB_HOST', '127.0.0.1'),
            'port'        => (string)Env::get('DB_PORT', '3306'),
            'database'    => (string)Env::get('DB_NAME', 'xb_code'),
            'username'    => (string)Env::get('DB_USER', 'root'),
            'password'    => (string)Env::get('DB_PASS', ''),
            'charset'     => (string)Env::get('DB_CHARSET', 'utf8mb4'),
            'collation'   => (string)Env::get('DB_CHARSET_CI', 'utf8mb4_general_ci'),
            'prefix'      => (string)Env::get('DB_PREFIX', ''),
            'strict'      => (bool)Env::get('DB_STRICT', true),
            'engine'      => Env::get('DB_ENGINE', null),
            'options'     => [
                PDO::ATTR_EMULATE_PREPARES => (bool)Env::get('DB_ATTR_EMULATE_PREPARES', false),
            ],
            'pool'        => [
                'max_connections'    => (int)Env::get('DB_POOL_MAX', 5),
                'min_connections'    => (int)Env::get('DB_POOL_MIN', 1),
                'wait_timeout'       => (int)Env::get('DB_POOL_WAIT', 3),
                'idle_timeout'       => (int)Env::get('DB_POOL_IDLE', 60),
                'heartbeat_interval' => (int)Env::get('DB_POOL_HB', 50),
            ],
        ],
    ],
];