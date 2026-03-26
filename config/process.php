<?php
global $argv;

$monitor = [
    app_path(),
    config_path(),
    base_path() . '/process',
    base_path() . '/support',
    base_path() . '/resource',
    base_path() . '/.env',
];
// 调试模式增加插件目录监听
if (env('APP_DEBUG', false)) {
    $monitor[] = base_path() . '/plugin';
}

return [
    // File update detection and automatic reload
    'monitor' => [
        'handler' => app\process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitor these directories
            'monitorDir' => array_merge($monitor, glob(
                base_path() . '/plugin/*/app'),
                glob(base_path() . '/plugin/*/config'),
                glob(base_path() . '/plugin/*/api')
            ),
            // Files with these suffixes will be monitored
            'monitorExtensions' => [
                'php',
                'html',
                'htm',
                'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/',
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',
            ]
        ]
    ]
];
