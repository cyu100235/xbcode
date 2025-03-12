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
    'children' => [
      [
        'title' => '上传设置',
        'plugin' => 'xbUpload',
        'path' => 'admin/Engine/index',
        'component' => 'table/index',
        'method' => 'GET',
        'type' => '20',
        'icon' => '',
        'params' => '',
        'is_show' => '20',
        'is_system' => '10',
        'children' =>
          [
            [
              'title' => '保存上传设置',
              'plugin' => 'xbUpload',
              'path' => 'admin/Engine/config',
              'component' => 'form/index',
              'method' => 'GET,PUT',
              'type' => '30',
              'icon' => '',
              'params' => '',
              'is_show' => '10',
              'is_system' => '10',
              'children' =>
                [
                ],
            ],
          ],
      ],
    ],
  ],
];