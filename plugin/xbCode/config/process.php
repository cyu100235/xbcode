<?php
use plugin\xbCode\api\Nginx;
use support\Log;
use support\Request;
use plugin\xbCode\app\process\Http;

return [
    'server' => [
        'handler'     => Http::class,
        'listen'      => 'http://0.0.0.0:' . Nginx::get('port', 39000),
        'count'       => max(1, (int)ceil(cpu_count() * 2)),
        'user'        => '',
        'group'       => '',
        'reusePort'   => false,
        'eventLoop'   => '',
        'context'     => [],
        'constructor' => [
            'requestClass' => Request::class,
            'logger'       => Log::channel('default'),
            'appPath'      => app_path(),
            'publicPath'   => public_path(),
        ],
    ],
];
