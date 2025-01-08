<?php
namespace app\validate;

use taoser\Validate;

class UploadConfigValidate extends Validate
{
    protected $rule = [
        'bucket' => 'require',
        'access_key' => 'require',
        'secret_key' => 'require',
        'domain' => 'require|url',
    ];

    protected $message = [
        'bucket.require' => '请输入空间名称',
        'access_key.require' => '请输入ACCESS_KEY',
        'secret_key.require' => '请输入SECRET_KEY',
        'domain.require' => '请输入空间域名',
        'domain.url' => '请输入正确的空间域名',
    ];
}
