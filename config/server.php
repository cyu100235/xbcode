<?php
return [
    'listen' => 'http://0.0.0.0:'.xbEnv('APP_PORT', 39600),
    'transport' => 'tcp',
    'context' => [],
    'name' => 'webman',
    'count' => cpu_count() * 4,
    'user' => '',
    'group' => '',
    'reusePort' => false,
    'event_loop' => '',
    'stop_timeout' => 2,
    'pid_file' => runtime_path() . '/webman.pid',
    'status_file' => runtime_path() . '/webman.status',
    'stdout_file' => runtime_path() . '/logs/stdout.log',
    'log_file' => runtime_path() . '/logs/workerman.log',
    'max_package_size' => 100 * 1024 * 1024
];
