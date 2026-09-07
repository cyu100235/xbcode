<?php
/**
 * 由 xbCode 安装向导生成：ThinkORM 配置
 * 所有配置通过 plugin\xbCode\api\Env 读取项目根目录 .env，
 * 支持运行期 set/put 动态覆盖 & 按前缀分组读取；类型按 .env 字面量自动解析。
 *
 * @copyright 贵州积木云网络科技有限公司
 */

use plugin\xbCode\api\Env;
use plugin\xbCode\base\BaseQuery;

return [
    'default'     => (string)Env::get('DB_CONNECTION', 'mysql'),
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type'            => (string)Env::get('DB_DRIVER', 'mysql'),
            // 服务器地址
            'hostname'        => (string)Env::get('DB_HOST', '127.0.0.1'),
            // 数据库名
            'database'        => (string)Env::get('DB_NAME', 'xb_code'),
            // 数据库用户名
            'username'        => (string)Env::get('DB_USER', 'root'),
            // 数据库密码
            'password'        => (string)Env::get('DB_PASS', ''),
            // 数据库连接端口
            'hostport'        => (string)Env::get('DB_PORT', '3306'),
            // 数据库连接参数
            'params'          => [
                \PDO::ATTR_TIMEOUT => (int)Env::get('DB_ATTR_TIMEOUT', 3),
            ],
            // 数据库编码默认采用 utf8mb4
            'charset'         => (string)Env::get('DB_CHARSET', 'utf8mb4'),
            // 数据库表前缀
            'prefix'          => (string)Env::get('DB_PREFIX', ''),
            // 断线重连
            'break_reconnect' => (bool)Env::get('DB_BREAK_RECONNECT', true),
            // 自定义基础查询类
            'query'           => BaseQuery::class,
            // 连接池配置
            'pool'            => [
                'max_connections'    => (int)Env::get('DB_POOL_MAX', 5),
                'min_connections'    => (int)Env::get('DB_POOL_MIN', 1),
                'wait_timeout'       => (int)Env::get('DB_POOL_WAIT', 3),
                'idle_timeout'       => (int)Env::get('DB_POOL_IDLE', 60),
                'heartbeat_interval' => (int)Env::get('DB_POOL_HB', 50),
            ],
        ],
    ],
    // 自定义分页类
    'paginator' => (string)Env::get('DB_PAGINATOR', ''),
];