<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\validate;

use Webman\Validation\Validator;

/**
 * 管理员数据验证器
 */
class AdminValidate extends Validator
{
    /**
     * 验证规则
     * @var array
     */
    protected array $rules = [
        'role_id' => 'required|integer',
        'username' => 'required|min:2|max:20',
        'password' => 'required|min:5|max:20',
        'nickname' => 'required|min:2|max:20',
        'originpwd' => 'required',
        'newpassword' => 'required|min:5|max:20',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected array $messages = [
        'role_id.required' => '请选择所属角色',
        'role_id.integer' => '所属角色参数错误',
        'username.required' => '请输入登录账号',
        'username.min' => '登录账号至少2位',
        'username.max' => '登录账号最多20位',
        'password.required' => '请输入登录密码',
        'password.min' => '登录密码至少5位',
        'password.max' => '登录密码最多20位',
        'nickname.required' => '请输入用户昵称',
        'nickname.min' => '用户昵称至少2位',
        'nickname.max' => '用户昵称最多20位',
        'originpwd.required' => '请输入原登录密码',
        'newpassword.required' => '请输入新的登录密码',
        'newpassword.min' => '新的登录密码至少5位',
        'newpassword.max' => '新的登录密码最多20位',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected array $scenes = [
        // 登录
        'login' => ['username', 'password'],
        // 添加
        'add' => ['role_id', 'username', 'password', 'nickname'],
        // 修改
        'edit' => ['role_id', 'username', 'nickname'],
    ];
}
