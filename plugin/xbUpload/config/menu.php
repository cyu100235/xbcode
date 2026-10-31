<?php

return [
    [
        'title' => '系统配置',
        'path' => 'Config',
        'type' => '10',
        'icon' => 'Setting',
        'params' => '',
        'is_show' => '20',
        'is_system' => '20',
        'sort' => 9999,
        'children' => [
            [
                'title' => '上传设置',
                'path' => 'EngineList',
                'type' => '20',
                'icon' => '',
                'params' => '',
                'is_show' => '20',
                'is_system' => '20',
                'sort' => 20,
                'children' => [
                    [
                        'title' => '引擎列表',
                        'path' => 'admin/Engine/index',
                        'type' => '20',
                        'icon' => '',
                        'params' => '',
                        'is_show' => '10',
                        'is_system' => '20',
                        'sort' => 0,
                        'children' => []
                    ],
                    [
                        'title' => '保存上传设置',
                        'path' => 'admin/Engine/config',
                        'type' => '30',
                        'icon' => '',
                        'params' => '',
                        'is_show' => '10',
                        'is_system' => '20',
                        'sort' => 0,
                        'children' => []
                    ],
                    [
                        'title' => '检测储存连接',
                        'path' => 'admin/Engine/check',
                        'type' => '30',
                        'icon' => '',
                        'params' => '',
                        'is_show' => '10',
                        'is_system' => '20',
                        'sort' => 0,
                        'children' => []
                    ],
                    [
                        'title' => '快速保存引擎配置',
                        'path' => 'admin/Engine/quickSave',
                        'type' => '30',
                        'icon' => '',
                        'params' => '',
                        'is_show' => '10',
                        'is_system' => '20',
                        'sort' => 0,
                        'children' => []
                    ],
                    [
                        'title' => '文件管理',
                        'path' => 'UploadManage',
                        'type' => '30',
                        'icon' => '',
                        'params' => '',
                        'is_show' => '10',
                        'is_system' => '20',
                        'sort' => 0,
                        'children' => [
                            [
                                'title' => '文件列表',
                                'path' => 'admin/Upload/index',
                                'type' => '20',
                                'icon' => '',
                                'params' => '',
                                'is_show' => '10',
                                'is_system' => '20',
                                'sort' => 0,
                                'children' => []
                            ],
                            [
                                'title' => '上传文件',
                                'path' => 'admin/Upload/upload',
                                'type' => '30',
                                'icon' => '',
                                'params' => '',
                                'is_show' => '10',
                                'is_system' => '20',
                                'sort' => 0,
                                'children' => []
                            ],
                            [
                                'title' => '分片上传',
                                'path' => 'admin/Upload/chunk',
                                'type' => '30',
                                'icon' => '',
                                'params' => '',
                                'is_show' => '10',
                                'is_system' => '20',
                                'sort' => 0,
                                'children' => []
                            ],
                            [
                                'title' => '修改文件',
                                'path' => 'admin/Upload/edit',
                                'type' => '30',
                                'icon' => '',
                                'params' => '',
                                'is_show' => '10',
                                'is_system' => '20',
                                'sort' => 0,
                                'children' => []
                            ],
                            [
                                'title' => '删除文件',
                                'path' => 'admin/Upload/del',
                                'type' => '30',
                                'icon' => '',
                                'params' => '',
                                'is_show' => '10',
                                'is_system' => '20',
                                'sort' => 0,
                                'children' => []
                            ],
                            [
                                'title' => '查看文件',
                                'path' => 'admin/Upload/show',
                                'type' => '30',
                                'icon' => '',
                                'params' => '',
                                'is_show' => '10',
                                'is_system' => '20',
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