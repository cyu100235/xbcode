<?php
// 数据库配置
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => 'mysql',
            // 服务器地址
            'hostname' => xbEnv('DB_HOST', '127.0.0.1'),
            // 数据库名
            'database' => xbEnv('DB_NAME', ''),
            // 数据库用户名
            'username' => xbEnv('DB_USER', ''),
            // 数据库密码
            'password' => xbEnv('DB_PASS', ''),
            // 数据库连接端口
            'hostport' => xbEnv('DB_PORT', '3306'),
            // 数据库连接参数
            'params' => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset' => xbEnv('DB_CHARSET', 'utf8mb4'),
            // 数据库表前缀
            'prefix' => xbEnv('DB_PREFIX', 'xb_'),
            // 断线重连
            'break_reconnect' => true,
            // 自定义基础查询类
            'query' => \xbcode\model\BaseQuery::class,
            // 自定义分页类
            'bootstrap' =>  '',
        ],
    ],
];
