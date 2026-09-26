<?php
return [
    'xbAdmin.Crontab.del' => [
        [\plugin\xbAdmin\app\events\CrontabLogEvent::class, 'delete'],
    ],
];