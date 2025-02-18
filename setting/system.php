<?php

return [
    [
        'field' => 'web_name',
        'title' => '网站名称',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'col' => 12,
            'prompt' => '应用名称，显示在浏览器标签页',
        ],
    ],
    [
        'field' => 'web_url',
        'title' => '网站域名',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'col' => 12,
            'prompt' => '网站链接，以斜杠结尾，如：https://xiaobai.host/',
        ],
    ],
    [
        'field' => 'web_title',
        'title' => '网站标题',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'col' => 12,
        ],
    ],
    [
        'field' => 'web_keywords',
        'title' => '网站关键字',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'col' => 12,
        ],
    ],
    [
        'field' => 'web_description',
        'title' => '网站描述',
        'value' => '',
        'type' => 'textarea',
        'extra' => [
            'rows' => 4,
            'resize' => 'none',
            'prompt' => '请勿手动换行，字数在100字以内',
        ],
    ],
    [
        'field' => 'web_logo',
        'title' => '系统图标',
        'value' => '',
        'type' => 'xbUpload',
        'extra' => [
            'col' => 8,
            'prompt' => '建议尺寸：300*300像素，支持jpg，jpeg，png格式',
            'props' => [
                'type' => 'image',
            ],
        ],
    ],
    [
        'field' => 'login_bg',
        'title' => '登录页背景',
        'value' => '',
        'type' => 'xbUpload',
        'extra' => [
            'col' => 8,
            'prompt' => '
            <div>尺寸：1920*1080像素</div>
            <div>支持jpg，jpeg，png图片与MP4视频格式</div>',
            'props' => [
                'type' => 'other',
            ],
        ],
    ],
    [
        'field' => 'login_ad',
        'title' => '登录页广告',
        'value' => '',
        'type' => 'xbUpload',
        'extra' => [
            'col' => 8,
            'prompt' => '
            <div>建议尺寸：400*400像素</div>
            <div>支持jpg，jpeg，png图片与MP4视频格式</div>',
            'props' => [
                'type' => 'other',
            ],
        ],
    ],
];
