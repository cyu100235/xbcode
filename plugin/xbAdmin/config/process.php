<?php
use Channel\Server;
use Workerman\Protocols\Frame;
use plugin\xbAdmin\api\ChannelClient;
use plugin\xbAdmin\app\process\Crontab;

$channelPort = ChannelClient::getPort();

return [
    // 定时任务服务
    'crontab'  => [
        'handler'  => Crontab::class,
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
