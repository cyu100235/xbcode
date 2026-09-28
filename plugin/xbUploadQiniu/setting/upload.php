<?php

use plugin\xbCode\builder\Components\Form\Hidden;
use plugin\xbUploadQiniu\app\validate\ConfigValidate;
use plugin\xbCode\builder\Components\Form\InputText;
use plugin\xbCode\builder\Components\Form\InputPassword;

return [
    Hidden::make()
        ->name('xbValidate')
        ->value(ConfigValidate::class)
        ->get(),
    InputText::make()
        ->name('bucket')
        ->label('空间名称')
        ->required(true)
        ->description('空间名称，即Bucket，空间访问权限必须设置为公开')
        ->get(),
    InputText::make()
        ->name('domain')
        ->label('空间域名')
        ->required(true)
        ->description('完整的域名地址，例如https://static.xbcode.net')
        ->get(),
    InputPassword::make()
        ->name('access_key')
        ->label('Access_Key')
        ->required(true)
        ->revealPassword(false)
        ->description('七牛云 - 右上角头像 - 密钥管理 - 复制AK')
        ->get(),
    InputPassword::make()
        ->name('secret_key')
        ->label('Access_Secret')
        ->required(true)
        ->revealPassword(false)
        ->description('七牛云 - 右上角头像 - 密钥管理 - 复制SK')
        ->get(),
];