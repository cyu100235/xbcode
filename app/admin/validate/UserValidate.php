<?php
namespace app\admin\validate;

use app\model\User;
use Tinywan\Validate\Validate;

class UserValidate extends Validate
{
    protected array $rule = [
        'id' => 'require',
        'username' => 'require|alphaNum|length:4,15|verifyunique',
        'scode' => 'require|length:4,6',
        'password' => 'require|length:4,20',
        'nickname' => 'require|length:2,10',
        'avatar' => 'require',
    ];

    protected array $message = [
        'id.require' => '缺少ID参数',
        'username.require' => '请输入用户名',
        'username.alphaNum' => '用户名必须是字母和数字',
        'username.length' => '用户名长度必须在4-15位之间',
        'scode.require' => '请输入验证码',
        'scode.length' => '验证码长度必须在4-6位之间',
        'password.require' => '请输入登录密码',
        'password.length' => '登录密码长度必须在4-20位之间',
        'nickname.require' => '请输入用户昵称',
        'nickname.length' => '用户昵称长度必须在2-10位之间',
        'avatar.require' => '请选择或上传用户头像',
    ];

    /**
     * 账号注册场景验证
     * @return UserValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneUsernameRegister()
    {
        return $this
            ->only([
                'username',
                'password',
                'nickname',
            ]);
    }

    /**
     * 短信注册场景验证
     * @return UserValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneSmsRegister()
    {
        return $this
            ->only([
                'username',
                'scode',
                'nickname',
            ]);
    }

    /**
     * 添加场景验证
     * @return UserValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function sceneAdd()
    {
        return $this
            ->only([
                'username',
                'password',
                'nickname',
            ]);
    }

    /**
     * 编辑场景验证
     * @return UserValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function sceneEdit()
    {
        return $this
            ->only([
                'id',
                'username',
                'password',
                'nickname',
            ])
            ->remove('username', ['verifyunique'])
            ->remove('password', ['require']);
    }

    /**
     * 验证唯一
     * @param mixed $value
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function verifyunique($value)
    {
        $result = User::where('username', $value)->find();
        if ($result) {
            return '该用户已注册';
        }
        return true;
    }
}
