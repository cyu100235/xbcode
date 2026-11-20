<?php
/**
 * 插件启动引导
 *
 * support\bootstrap.php 会先执行各插件 config/autoload.php 声明的 files，
 * 再执行 bootstrap 类；缓存隔离必须早于任何 Redis / 缓存连接创建，
 * 故挂在 autoload 而非 bootstrap 清单里。
 */
return [
    'files' => [
        base_path() . '/plugin/xbCode/app/functions.php',
        base_path() . '/plugin/xbCode/bootstrap/cache-isolation.php',
    ]
];
