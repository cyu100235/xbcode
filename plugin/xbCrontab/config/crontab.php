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
        'title' => '定期清理定时日志',
        'name' => 'crontab_clear_log',
        'plugin' => 'xbCrontab',
        'type' => '10',
        'cron_expression' => '0 5 * * 0',
        'cron_desc' => '每周周日 5:00',
        'command' => 'php webman crontab:clear:log'
    ],
    [
        'title' => '清除系统文件日志',
        'name' => 'file_clear_log',
        'plugin' => 'xbCrontab',
        'type' => '10',
        'cron_expression' => '0 5 * * 0',
        'cron_desc' => '每周周日 5:00',
        'command' => 'php webman file:clear:log'
    ]
];
