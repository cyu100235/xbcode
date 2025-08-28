<?php

return [
    [
        'field' => 'about_name',
        'title' => '组织名称',
        'value' => '',
        'type' => 'InputText',
        'extra' => [
            'desc' => '示例：贵州积木云网络科技有限公司',
            'style' => [
                'width' => '30%',
            ],
        ],
    ],
    [
        'field' => 'about_url',
        'title' => '组织链接',
        'value' => '',
        'type' => 'InputText',
        'extra' => [
            'desc' => '示例：http://www.xbcode.net',
            'style' => [
                'width' => '30%',
            ],
        ],
    ],
    [
        'field' => 'copyright',
        'title' => '版权信息',
        'value' => '',
        'type' => 'InputText',
        'extra' => [
            'desc' => "变量：{ABOUT_NAME} {ABOUT_URL} 示例：copyright © 2023-2025 {ABOUT_NAME} All Rights Reserved",
            'style' => [
                'width' => '30%',
            ],
        ],
    ],
    [
        'field' => 'web_icp',
        'title' => 'ICP备案号码',
        'value' => '',
        'type' => 'InputText',
        'extra' => [
            'desc' => '示例：贵ICP备12345678号',
            'style' => [
                'width' => '30%',
            ],
        ],
    ],
    [
        'field' => 'web_police',
        'title' => '公安备案号码',
        'value' => '',
        'type' => 'InputText',
        'extra' => [
            'desc' => '示例：贵公网安备123456789号',
            'style' => [
                'width' => '30%',
            ],
        ],
    ],
    [
        'field' => 'web_police_code',
        'title' => '公安备案编号',
        'value' => '',
        'type' => 'InputText',
        'extra' => [
            'desc' => '示例：52010502005838',
            'style' => [
                'width' => '30%',
            ],
        ],
    ],
];