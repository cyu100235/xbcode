<?php
global $argv;

return [
    // 文件更新检测与自动重新加载
    'monitor' => [
        'handler' => app\process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // 监控这些目录
            'monitorDir' => array_values(array_filter(array_merge([
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], (array)glob(base_path() . '/plugin/*', GLOB_ONLYDIR), (array)glob(base_path() . '/plugin/*/*', GLOB_ONLYDIR)))),
            // 带有这些后缀的文件将会被监控
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',
            ]
        ]
    ]
];