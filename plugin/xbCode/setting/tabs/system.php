<?php

use plugin\xbCode\enum\SwitchEnum;
use plugin\xbCode\builder\Components\Form\FieldSet;
use plugin\xbCode\builder\Components\Form\InputText;
use plugin\xbCode\builder\Components\Form\InputImage;
use plugin\xbCode\builder\Components\ButtonGroupSelect;

return [
    FieldSet::make()
    ->name('system')
    ->title('基本配置')
    ->setVariable('sort', 0)
    ->body([
        InputText::make()
        ->name('web_name')
        ->label('网站名称')
        ->description('应用名称，显示在浏览器标签页')
        ->style([
            'width' => '40%'
        ])
        ->get(),
        InputText::make()
        ->name('web_url')
        ->label('网站标题')
        ->description('网站标题，显示在浏览器标签页')
        ->style([
            'width' => '40%'
        ])
        ->get(),
        InputText::make()
        ->name('web_title')
        ->label('网站域名')
        ->description('非必要情况，请勿修改域名，否则可能导致部分功能异常，以斜杠结尾，示例：https://xiaobai.host/')
        ->style([
            'width' => '40%'
        ])
        ->get(),
        InputText::make()
        ->name('web_keywords')
        ->label('网站关键词')
        ->description('网站关键词，可以用于SEO优化')
        ->style([
            'width' => '40%'
        ])
        ->get(),
        InputText::make()
        ->name('web_desc')
        ->label('网站描述')
        ->description('网站描述，可以用于SEO优化')
        ->style([
            'width' => '40%'
        ])
        ->get(),
        InputImage::make()
        ->name('web_logo')
        ->label('站点图标')
        ->description('建议尺寸：300*300像素，支持jpg，jpeg，png格式')
        ->get(),
        InputImage::make()
        ->name('login_bg')
        ->label('登录页背景')
        ->description('建议尺寸：1920*1080像素，支持jpg、jpeg、png格式图片与MP4视频')
        ->get(),
        InputImage::make()
        ->name('login_ad')
        ->label('登录页广告')
        ->description('建议尺寸：400*400像素，支持jpg、jpeg、png格式图片与MP4视频')
        ->get(),
        ButtonGroupSelect::make()
        ->name('captcha_state')
        ->label('登录验证码')
        ->value('10')
        ->description('登录后台是否开启必须验证码')
        ->options(SwitchEnum::options())
        ->get(),
    ])
    ->get(),
];