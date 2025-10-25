<?php
// 环境检测配置
return [
    [
        'title' => '操作系统',
        'type'    => 'system',
        'status' => false,
        'value' => [
            'Linux',
            'Windows'
        ],
    ],
    [
        'title' => '磁盘空间',
        'type'    => 'disk',
        'status' => false,
        'value' => 300,
    ],
    [
        'title'   => 'php版本',
        'name'    => 'php',
        'min'     => '8.1',
        'max'     => '8.2',
        'type'    => 'version',
        'status'  => false,
        'value'   => '最低PHP8.0以上版本'
    ],
    [
        'title'   => 'mysql版本',
        'name'    => 'mysql',
        'type'    => 'mysql',
        'status'  => false,
        'value'   => '建议使用5.7版本'
    ],
];