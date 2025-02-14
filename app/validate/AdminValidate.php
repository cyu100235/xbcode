<?php
namespace plugin\xbCode\app\validate;

use taoser\Validate;

/**
 * 管理员验证器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminValidate extends Validate
{
    protected $rule = [
        'role_id' => 'require',
        'username' => 'require|length:2,20',
        'originpwd' => 'require',
        'password' => 'require|length:5,20',
        'newpassword' => 'require',
        'nickname' => 'require|length:2,20',
        'avatar' => 'require',
    ];

    protected $message = [
        'role_id.require' => '请选择角色',
        'username.require' => '请输入登录账号',
        'username.length' => '登录账号字数必须在2-20位之间',
        'originpwd.require' => '请输入旧的登录密码',
        'password.require' => '请输入登录密码',
        'password.length' => '登录密码字数必须在5-20位之间',
        'newpassword.require' => '请输入新的登录密码',
        'nickname.require' => '请输入用户昵称',
        'nickname.length' => '用户昵称字数必须在2-20位之间',
        'avatar.require' => '请上传并选择头像',
    ];

    /**
     * 登录
     * @return AdminValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneLogin()
    {
        return $this->only([
            'username',
            'password',
        ]);
    }

    /**
     * 添加
     * @return AdminValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneAdd()
    {
        return $this->only([
            'role_id',
            'username',
            'password',
            'nickname',
            'avatar'
        ]);
    }

    /**
     * 编辑
     * @return AdminValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneEdit()
    {
        return $this->only([
            'role_id',
            'username',
            'nickname',
            'avatar'
        ])
        ->remove('username','unique')
        ->remove('nickname','unique');
    }

    /**
     * 个人资料
     * @return AdminValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneProfile()
    {
        return $this->only([
            'username',
            'originpwd',
            'newpassword',
            'nickname',
            'avatar'
        ])
        ->remove('username','unique')
        ->remove('nickname','unique');
    }
}
