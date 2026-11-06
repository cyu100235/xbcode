<?php
/**
 * 定时任务配置文件
 * title 任务名称
 * name 任务标识
 * plugin 所属插件
 * type 任务类型
 * cron_expression Cron表达式
 * cron_desc 周期描述
 * command 执行命令
 */
return [
    [
        'title' => '管理员登录空闲检测',
        'name' => 'admin:check:idle',
        'plugin' => 'xbCode',
        'type' => '10',
        'cron_expression' => '*/30 * * * *',
        'cron_desc' => '每30分钟',
        'command' => 'php webman admin:check:idle'
    ]
];
