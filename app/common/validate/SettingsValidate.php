<?php

namespace app\common\validate;

use think\Validate;

class SettingsValidate extends Validate
{
    protected $rule =   [
        'title'                     => 'require',
        'field'                     => 'require',
        'component'                 => 'require',
    ];

    protected $message  =   [
        'title.require'             => '配置项标识不能为空',
        'field.require'             => '配置项名称不能为空',
        'component.require'         => '表单组件不能为空',
    ];
}
