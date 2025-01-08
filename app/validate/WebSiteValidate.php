<?php
namespace app\validate;

use taoser\Validate;

/**
 * 站点验证器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebSiteValidate extends Validate
{
    protected $rule = [
        'title' => 'require',
        'domain' => 'require|verifyDomain',
        'username' => 'require|alphaNum|length:5,20',
        'password' => 'require|length:5,20',
    ];

    protected $message = [
        'title.require' => '请填写站点名称',
        'domain.require' => '请填写绑定域名',
        'username.require' => '请填写登录账号',
        'username.length' => '登录账号长度为5-20位',
        'username.alphaNum' => '登录账号只能是字毅和数字',
        'password.require' => '请填写登录密码',
        'password.length' => '登录密码长度为5-20位',
    ];

    /**
     * 添加场景
     * @return WebSiteValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneAdd()
    {
        return $this->only([
            'title',
            'domain',
            'username',
            'password'
        ]);
    }

    /**
     * 修改场景
     * @return WebSiteValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneEdit()
    {
        return $this->only([
            'title',
            'domain',
            'username',
        ]);
    }

    /**
     * 验证域名
     * @param mixed $value
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function verifyDomain($value)
    {
        if (!preg_match('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/', $value)) {
            return '请填写正确的域名';
        }
        return true;
    }
}
