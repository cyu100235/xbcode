<?php

use plugin\xbCode\builder\Components\Form\Hidden;
use plugin\xbUploadCos\app\validate\ConfigValidate;
use plugin\xbCode\builder\Components\Form\FieldSet;
use plugin\xbCode\builder\Components\Form\InputText;
use plugin\xbCode\builder\Components\Form\InputPassword;

return [
    FieldSet::make()
        ->title('储存设置')
        ->setVariable('sort', 0)
        ->body([
            Hidden::make()
                ->name('xbValidate')
                ->value(ConfigValidate::class)
                ->get(),
            InputText::make()
                ->name('bucket')
                ->label('空间名称')
                ->required(true)
                ->description('空间名称，空间访问权限必须设置为公开')
                ->get(),
            InputText::make()
                ->name('region')
                ->label('所属地域')
                ->required(true)
                ->description('例如：ap-guangzhou')
                ->get(),
            InputText::make()
                ->name('domain')
                ->label('域名地址')
                ->required(true)
                ->description('完整的域名地址，例如：https://static.cloud.com')
                ->get(),
            InputPassword::make()
                ->name('secret_id')
                ->label('SECRET_Id')
                ->required(true)
                ->revealPassword(false)
                ->description('腾讯云SECRET_Id')
                ->get(),
            InputPassword::make()
                ->name('secret_key')
                ->label('SECRET_KEY')
                ->required(true)
                ->revealPassword(false)
                ->description('腾讯云SECRET_KEY')
                ->get(),
        ])
        ->get(),
];