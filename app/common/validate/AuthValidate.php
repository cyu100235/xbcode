<?php

namespace app\common\validate;

use think\Validate;

/**
 * 鉴权加密验证
 * @author 贵州小白基地网络科技有限公司
 * @copyright (c) 2023
 */
class AuthValidate extends Validate
{
    protected $rule =   [
        'id'                    => 'require',
        'role_id'               => 'require',
        'pid'                   => 'require',
        'status'                => 'require',
        'is_system'             => 'require',
    ];

    protected $message  =   [
        'id.require'            => '缺少用户ID',
        'role_id.require'       => '缺少角色权限ID',
        'status.require'        => '缺少状态参数',
        'is_system.require'     => '缺少是否系统参数',
    ];
}
