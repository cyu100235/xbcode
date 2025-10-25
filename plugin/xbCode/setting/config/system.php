<?php

use plugin\xbCode\enum\SwitchEnum;

return [
    [
        'field' => 'web_name',
        'title' => '网站名称',
        'value' => '',
        'type' => 'InputText',
        'extra' => [
            'desc' => '应用名称，显示在浏览器标签页',
            'style' => [
                'width' => '30%',
            ],
        ],
    ],
    [
        'field' => 'web_url',
        'title' => '网站域名',
        'value' => '',
        'type' => 'InputText',
        'extra' => [
            'desc' => '非必要情况，请勿修改域名，否则可能导致部分功能异常，以斜杠结尾，示例：https://xiaobai.host/',
            'style' => [
                'width' => '30%',
            ],
        ],
    ],
    [
        'field' => 'web_desc',
        'title' => '网站描述',
        'value' => '',
        'type' => 'Textarea',
        'extra' => [
            'desc' => '网站描述，可以用于SEO优化',
            'style' => [
                'width' => '30%',
            ],
        ],
    ],
    [
        'field' => 'web_logo',
        'title' => '站点图标',
        'value' => '',
        'type' => 'InputImage',
        'extra' => [
            'desc' => '建议尺寸：300*300像素，支持jpg，jpeg，png格式',
        ],
    ],
    [
        'field' => 'login_bg',
        'title' => '登录页背景',
        'value' => '',
        'type' => 'InputImage',
        'extra' => [
            'desc' => '建议尺寸：1920*1080像素，支持jpg、jpeg、png格式图片与MP4视频',
        ],
    ],
    [
        'field' => 'login_ad',
        'title' => '登录页广告',
        'value' => '',
        'type' => 'InputImage',
        'extra' => [
            'desc' => '建议尺寸：400*400像素，支持jpg、jpeg、png格式图片与MP4视频',
        ],
    ],
    [
        'field' => 'captcha_state',
        'title' => '登录验证码',
        'value' => '10',
        'type' => 'Radios',
        'extra' => [
            'desc' => '登录后台是否开启必须验证码',
            'options' => SwitchEnum::options(),
        ],
    ],
];
