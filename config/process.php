<?php
use support\Log;
use support\Request;
use plugin\xbCode\process\Http;

return [
    'server' => [
        'handler' => Http::class,
        'listen' => 'http://0.0.0.0:39800',
        'count' => cpu_count() * 4,
        'user' => '',
        'group' => '',
        'reusePort' => false,
        'eventLoop' => '',
        'context' => [],
        'constructor' => [
            'requestClass' => Request::class,
            'logger' => Log::channel('default'),
            'appPath' => app_path(),
            'publicPath' => public_path()
        ]
    ]
];
