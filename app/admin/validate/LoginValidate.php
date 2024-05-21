<?php
namespace app\admin\validate;

use Tinywan\Validate\Validate;

/**
 * 登录验证器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class LoginValidate extends Validate
{
    protected array $rule = [
        'username'          => 'require',
        'password'          => 'require',
    ];

    protected array $message = [
        'username.require'          => '请输入登录账号',
        'password.require'          => '请输入登录密码',
    ];
}
