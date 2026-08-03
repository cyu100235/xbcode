<?php
return [
    'xbCrontab.Crontab.del' => [
        [\plugin\xbCrontab\app\events\CrontabLogEvent::class, 'delete'],
    ],
];