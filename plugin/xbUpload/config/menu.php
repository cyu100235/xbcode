<?php

return [
    [
        'title' => '系统配置',
        'plugin' => 'xbCode',
        'path' => 'Config',
        'type' => '10',
        'icon' => 'Setting',
        'params' => '',
        'is_show' => '20',
        'is_system' => '10',
        'state' => '20',
        'sort' => 9999,
        'children' => [
            [
                'title' => '上传设置',
                'plugin' => 'xbUpload',
                'path' => 'admin/Engine/index',
                'type' => '20',
                'icon' => '',
                'params' => '',
                'is_show' => '20',
                'is_system' => '10',
                'state' => '20',
                'sort' => 20,
                'children' => [
                    [
                        'title' => '保存上传设置',
                        'plugin' => 'xbUpload',
                        'path' => 'admin/Engine/config',
                        'type' => '30',
                        'icon' => '',
                        'params' => '',
                        'is_show' => '10',
                        'is_system' => '10',
                        'state' => '20',
                        'sort' => 0,
                        'children' => []
                    ],
                    [
                        'title' => '文件管理',
                        'plugin' => 'xbUpload',
                        'path' => 'admin/Upload/index',
                        'type' => '30',
                        'icon' => '',
                        'params' => '',
                        'is_show' => '10',
                        'is_system' => '10',
                        'state' => '20',
                        'sort' => 0,
                        'children' => [
                            [
                                'title' => '上传文件',
                                'plugin' => 'xbUpload',
                                'path' => 'admin/Upload/upload',
                                'type' => '30',
                                'icon' => '',
                                'params' => '',
                                'is_show' => '10',
                                'is_system' => '10',
                                'state' => '20',
                                'sort' => 0,
                                'children' => []
                            ],
                            [
                                'title' => '修改文件',
                                'plugin' => 'xbUpload',
                                'path' => 'admin/Upload/edit',
                                'type' => '30',
                                'icon' => '',
                                'params' => '',
                                'is_show' => '10',
                                'is_system' => '10',
                                'state' => '20',
                                'sort' => 0,
                                'children' => []
                            ],
                            [
                                'title' => '删除文件',
                                'plugin' => 'xbUpload',
                                'path' => 'admin/Upload/del',
                                'type' => '30',
                                'icon' => '',
                                'params' => '',
                                'is_show' => '10',
                                'is_system' => '10',
                                'state' => '20',
                                'sort' => 0,
                                'children' => []
                            ],
                            [
                                'title' => '查看文件',
                                'plugin' => 'xbUpload',
                                'path' => 'admin/Upload/show',
                                'type' => '30',
                                'icon' => '',
                                'params' => '',
                                'is_show' => '10',
                                'is_system' => '10',
                                'state' => '20',
                                'sort' => 0,
                                'children' => []
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];