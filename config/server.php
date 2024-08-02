<?php
use app\common\utils\FrameUtil;

return [
    'listen' => 'http://0.0.0.0:' . FrameUtil::getServerPort(),
    'transport' => 'tcp',
    'context' => [],
    'name' => 'xbase',
    'count' => cpu_count() * 2,
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
