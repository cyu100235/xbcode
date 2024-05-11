<?php

return [
    [
        'field'         => 'web_name',
        'title'         => '网站名称',
        'value'         => '',
        'component'     => 'input',
        'extra'         => [
            'col'       => 12,
            'prompt'        => '应用名称，显示在浏览器标签页',
        ],
    ],
    [
        'field'         => 'web_url',
        'title'         => '网站域名',
        'value'         => '',
        'component'     => 'input',
        'extra'         => [
            'col'       => 12,
            'prompt'        => '网站链接，以斜杠结尾，如：https://xiaobai.host/',
        ],
    ],
    [
        'field'         => 'web_title',
        'title'         => '网站标题',
        'value'         => '',
        'component'     => 'input',
        'extra'         => [
            'col'       => 12,
        ],
    ],
    [
        'field'         => 'web_keywords',
        'title'         => '网站关键字',
        'value'         => '',
        'component'     => 'input',
        'extra'         => [
            'col'       => 12,
        ],
    ],
    [
        'field'         => 'web_description',
        'title'         => '网站描述',
        'value'         => '',
        'component'     => 'textarea',
        'extra'         => [
            'rows'      => 4,
            'resize'    => 'none',
            'prompt'    => '请勿手动换行，字数在100字以内',
        ],
    ],
    [
        'field'         => 'web_logo',
        'title'         => '系统图标',
        'value'         => '',
        'component'     => 'uploadify',
        'extra'         => [],
    ],
];
