<?php
use support\Log;
use support\Request;
use plugin\xbCode\process\Http;
use plugin\xbCode\utils\FrameUtil;

$serverPort = FrameUtil::xbServerPort();

// 监听地址：默认 0.0.0.0 以便局域网 / 真机联调访问，
// 需要仅本机访问时可在根目录 .env 中设置 SERVER_LISTEN_HOST = 127.0.0.1
$listenHost = env('SERVER_LISTEN_HOST', '0.0.0.0');

return [
    'server' => [
        'handler' => Http::class,
        'listen' => "http://{$listenHost}:{$serverPort}",
        'count' => cpu_count(),
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
    ],
    // 队列进程（自动发现所有插件消费者），无需手动配置目录
    'queue' => [
        // 使用自定义多目录消费者进程，自动扫描 plugin/*/app/queue
        'handler' => plugin\xbCode\process\MultiConsumer::class,
        // 进程数量
        'count' => 8,
    ]
];
