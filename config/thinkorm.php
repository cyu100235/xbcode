<?php

return [
    'default' => xbEnv('DATABASE.TYPE', 'mysql'),
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => xbEnv('DATABASE.TYPE', 'mysql'),
            // 服务器地址
            'hostname' => xbEnv('DATABASE.HOSTNAME', '127.0.0.1'),
            // 数据库名
            'database' => xbEnv('DATABASE.DATABASE', 'xbase'),
            // 数据库用户名
            'username' => xbEnv('DATABASE.USERNAME', 'root'),
            // 数据库密码
            'password' => xbEnv('DATABASE.PASSWORD', ''),
            // 数据库连接端口
            'hostport' => xbEnv('DATABASE.HOSTPORT', '3306'),
            // 数据库连接参数
            'params' => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset' => xbEnv('DATABASE.CHARSET', 'utf8mb4'),
            // 数据库表前缀
            'prefix' => xbEnv('DATABASE.PREFIX', 'xb_'),
            // 断线重连
            'break_reconnect' => true,
            // 关闭SQL监听日志
            'trigger_sql' => xbEnv('APP_DEBUG', true),
            // 自定义基础查询类
            'query' => \app\common\base\BaseQuery::class,
            // 自定义分页类
            'bootstrap' => ''
        ],
    ],
];
