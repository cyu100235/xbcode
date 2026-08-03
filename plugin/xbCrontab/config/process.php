<?php
use Webman\Channel\Server;
use Workerman\Protocols\Frame;
use plugin\xbCrontab\api\ChannelClient;
use plugin\xbCrontab\app\process\XbCrontab;

$channelPort = ChannelClient::getPort();

return [
    // 定时任务服务
    'crontab'  => [
        'handler'  => XbCrontab::class,
    ],
    // Channel服务
    'channel' => [
        'listen' => "frame://127.0.0.1:{$channelPort}",
        'protocol' => Frame::class,
        'handler' => Server::class,
        'reloadable' => false,
        'count' => 1, // 必须是1
    ]
];
