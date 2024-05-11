<?php
namespace app\admin\validate;

use app\model\Admin;
use Tinywan\Validate\Validate;

class AdminValidate extends Validate
{
    protected array $rule = [
        'role_id'           => 'require',
        'username'          => 'require|verifyAdd',
        'password'          => 'require',
        'nickname'          => 'require',
        'avatar'            => 'require',
    ];

    protected array $message = [
        'role_id.require'       => '请选择所属部门',
        'username.require'      => '请输入登录账号',
        'password.require'      => '请输入登录密码',
        'nickname.require'      => '请输入用户昵称',
        'avatar.require'        => '请上传头像',
    ];

    /**
     * 登录场景
     * @return Validate
     * @copyright 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function sceneLogin()
    {
        return $this
            ->only([
                'username',
                'password',
            ])
            ->remove('username', ['verifyAdd']);
    }

    /**
     * 添加场景验证
     * @return Validate
     * @copyright 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function sceneAdd()
    {
        return $this
            ->only([
                'role_id',
                'username',
                'password',
                'nickname',
                'avatar',
            ]);
    }

    /**
     * 修改场景
     * @return Validate
     * @copyright 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function sceneEdit()
    {
        return $this
            ->only([
                'role_id',
                'username',
                'nickname',
                'avatar',
            ])
            ->remove('username', ['verifyAdd']);
    }

    /**
     * 修改自身数据
     * @return Validate
     * @copyright 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function sceneEditSelf()
    {
        return $this->only([
            'username',
            'nickname',
            'avatar'
        ])
        ->remove('role_id')
        ->remove('username', ['verifyAdd']);
    }

    /**
     * 验证是否存在
     * @param mixed $value
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    protected function verifyAdd($value)
    {
        $where = [
            'username' => $value
        ];
        if (Admin::where($where)->count()) {
            return '该用户已存在';
        }
        return true;
    }
}
