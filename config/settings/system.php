<?php

return [
    [
        'field'         => 'web_name',
        'title'         => '网站名称',
        'value'         => '',
        'component'     => 'input',
        'extra'         => [],
    ],
    [
        'field'         => 'web_url',
        'title'         => '网站域名',
        'value'         => '',
        'component'     => 'input',
        'extra'         => [
            'props'     => [
                'type'          => 'text',
            ],
            'prompt'        => [
                'text'      => '网站链接，以斜杠结尾，如：https://www.xiaobai.host/'
            ],
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
