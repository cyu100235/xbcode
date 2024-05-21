<?php
namespace app\admin\validate;

use Tinywan\Validate\Validate;

class SettingsValidate extends Validate
{
    protected array $rule =   [
        'field'                     => 'require',
        'component'                 => 'require',
    ];

    protected array $message  =   [
        'field.require'             => '配置项名称不能为空',
        'component.require'         => '表单组件不能为空',
    ];
}
