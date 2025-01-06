<?php

use app\model\WebSite;

$data = [
    [
        'title' => '基础设置',
        'type' => 'xbTitle',
    ],
    [
        'field' => 'web_name',
        'title' => '系统名称',
        'value' => '',
        'type' => 'input',
        'extra' => [
            'col' => 12,
            'prompt' => '应用名称，显示在浏览器标签页',
        ],
    ],
    [
        'field' => 'web_url',
        'title' => '系统域名',
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
            'prompt' => "建议尺寸：300*300像素\n支持jpg，jpeg，png格式",
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
            'prompt' => "尺寸：1920*1080像素\n支持jpg，jpeg，png图片与MP4视频格式",
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
            'prompt' => "建议尺寸：400*400像素\n支持jpg，jpeg，png图片与MP4视频格式",
            'props' => [
                'type' => 'other',
            ],
        ],
    ],
];

// 设置版权
$copyrightState = WebSite::copyrightValue();
if ($copyrightState) {
    $data = array_merge($data, [
        [
            'title' => '版权设置',
            'type' => 'xbTitle',
        ],
        [
            'field' => 'about_name',
            'title' => '组织名称',
            'value' => '',
            'type' => 'input',
            'extra' => [
                'col' => 12,
                'prompt' => '示例：北京某某科技有限公司，不填写则不显示',
            ],
        ],
        [
            'field' => 'about_url',
            'title' => '组织网址',
            'value' => '',
            'type' => 'input',
            'extra' => [
                'col' => 12,
                'prompt' => '示例：http://www.example.com，不填写则不显示',
            ],
        ],
        [
            'field' => 'beian_text',
            'title' => '备案信息',
            'value' => '',
            'type' => 'input',
            'extra' => [
                'col' => 12,
                'prompt' => '示例：京ICP备12345678号，不填写则不显示',
            ],
        ],
        [
            'field' => 'beian_url',
            'title' => '备案链接',
            'value' => '',
            'type' => 'input',
            'extra' => [
                'col' => 12,
                'prompt' => '示例：http://www.miitbeian.gov.cn，不填写则不显示',
            ],
        ],
        [
            'field' => 'police_beian_text',
            'title' => '公安备案',
            'value' => '',
            'type' => 'input',
            'extra' => [
                'col' => 12,
                'prompt' => '示例：贵公网安备 5201031561656号，不填写则不显示',
            ],
        ],
        [
            'field' => 'police_beian_url',
            'title' => '公安链接',
            'value' => '',
            'type' => 'input',
            'extra' => [
                'col' => 12,
                'prompt' => '示例：http://www.miitbeian.gov.cn，不填写则不显示',
            ],
        ],
    ]);
}
return $data;