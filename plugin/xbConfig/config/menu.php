<?php

return [
    [
        'title' => '系统配置',
        'plugin' => 'xbCode',
        'path' => 'Config',
        'component' => 'none/index',
        'method' => 'GET',
        'type' => '10',
        'icon' => 'DashboardOutlined',
        'params' => '',
        'is_show' => '20',
        'is_system' => '10',
        'children' =>
            [
                [
                    'title' => '系统设置',
                    'plugin' => 'xbConfig',
                    'path' => 'admin/Setting/config/xbConfig/system',
                    'component' => 'form/index',
                    'method' => 'GET,PUT',
                    'type' => '20',
                    'icon' => '',
                    'params' => '',
                    'is_show' => '20',
                    'is_system' => '10',
                    'children' =>[],
                ],
            ],
    ],
];