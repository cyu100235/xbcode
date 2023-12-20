<?php

namespace app\admin\validate;

use app\common\model\Projects;
use think\Validate;

class ProjectValidate extends Validate
{
    protected $rule =   [
        'title'             => 'require|verifyTitle',
        'name'              => 'require',
        'username'          => 'require',
        'password'          => 'require',
        'logo'              => 'require',
    ];

    protected $message  =   [
        'title.require'             => '请输入项目名称',
        'name.require'              => '请选择应用插件',
        'username.require'          => '请输入管理员账号',
        'password.require'          => '请输入管理员密码',
        'logo.require'              => '请上传应用图标',
    ];

    public function sceneAdd()
    {
        return $this
            ->only([
                'title',
                'username',
                'password',
                'name',
                'logo',
            ]);
    }
    public function sceneEdit()
    {
        return $this
            ->only([
                'title',
                'name',
                'username',
                'logo',
            ])
            ->remove('title', ['verifyTitle']);
    }

    /**
     * 添加验证
     * @param mixed $value
     * @return bool|string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-05-11
     */
    protected function verifyTitle($value)
    {
        $where = [
            'title'         => $value
        ];
        if (Projects::where($where)->count()) {
            return '该应用已添加';
        }
        return true;
    }
}
