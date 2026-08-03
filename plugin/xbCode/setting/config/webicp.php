<?php

use plugin\xbCode\builder\Components\Form\InputText;

return [
    InputText::make()
        ->name('about_name')
        ->label('组织名称')
        ->required(true)
        ->description('示例：贵州积木云网络科技有限公司')
        ->style([
            'width' => '40%'
        ])
        ->get(),
    InputText::make()
        ->name('about_url')
        ->label('组织链接')
        ->required(true)
        ->description('示例：http://www.xbcode.net')
        ->style([
            'width' => '40%'
        ])
        ->get(),
    InputText::make()
        ->name('copyright')
        ->label('版权信息')
        ->required(true)
        ->description(
            <<<HTML
            <b style="color:#000;">底部版权替换文字说明，不设置则不显示版权（支持HTML标签）</b>
            <div style="line-height:25px;">
                <div>
                    示例如下：
                </div>
                <div>
                    copyright © 2023-2025 {ABOUT_NAME} All Rights Reserved &lt;a href="https://beian.miit.gov.cn/"&gt;{WEB_ICP}&lt;/a&gt;
                </div>
                <div>{WEB_NAME} = 网站名称</div>
                <div>{WEB_URL} = 网站域名</div>
                <div>{ABOUT_NAME} = 组织名称</div>
                <div>{ABOUT_URL} = 组织链接</div>
                <div>{WEB_ICP} = 备案号码</div>
                <div>{WEB_POLICE} = 公安备案号码</div>
                <div>{WEB_POLICE_CODE} = 公安备案编号</div>
            </div>
            HTML
        )
        ->style([
            'width' => '40%'
        ])
        ->get(),
    InputText::make()
        ->name('web_icp')
        ->label('ICP备案号码')
        ->required(true)
        ->description('示例：贵ICP备12345678号')
        ->style([
            'width' => '40%'
        ])
        ->get(),
    InputText::make()
        ->name('web_police')
        ->label('公安备案号码')
        ->required(true)
        ->description('示例：贵公网安备123456789号')
        ->style([
            'width' => '40%'
        ])
        ->get(),
    InputText::make()
        ->name('web_police_code')
        ->label('公安备案编号')
        ->required(true)
        ->description('示例：52010502005838')
        ->style([
            'width' => '40%'
        ])
        ->get(),
];