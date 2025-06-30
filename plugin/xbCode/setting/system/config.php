<?php

return [
    [
        'field' => 'web_name',
        'title' => '网站名称',
        'value' => '',
        'type' => 'InputText',
        'extra' => [
            'description' => '应用名称，显示在浏览器标签页',
        ],
    ],
    [
        'field' => 'web_url',
        'title' => '网站域名',
        'value' => '',
        'type' => 'InputText',
        'extra' => [
            'description' => '网站链接，以斜杠结尾，如：https://xiaobai.host/',
        ],
    ],
    [
        'field' => 'web_desc',
        'title' => '网站描述',
        'value' => '',
        'type' => 'Textarea',
        'extra' => [
            'description' => '网站描述，可以用于SEO优化',
        ],
    ],
    [
        'field' => 'web_logo',
        'title' => '站点图标',
        'value' => '',
        'type' => 'InputImage',
        'extra' => [
            'description' => '建议尺寸：300*300像素，支持jpg，jpeg，png格式',
        ],
    ],
    [
        'field' => 'login_bg',
        'title' => '登录页背景',
        'value' => '',
        'type' => 'InputImage',
        'extra' => [
            'description' => '建议尺寸：1920*1080像素，支持jpg、jpeg、png格式图片与MP4视频',
        ],
    ],
    [
        'field' => 'login_ad',
        'title' => '登录页广告',
        'value' => '',
        'type' => 'InputImage',
        'extra' => [
            'description' => '建议尺寸：400*400像素，支持jpg、jpeg、png格式图片与MP4视频',
        ],
    ],
];
