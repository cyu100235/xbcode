<?php

namespace app\admin\validate;

use app\common\model\Projects;
use think\Validate;

class ProjectValidate extends Validate
{
    protected $rule =   [
        'title'             => 'require|verifyTitle',
        'name'              => 'require|alpha|verifyName|unique:projects',
        'username'          => 'require',
        'password'          => 'require',
        'app_name'          => 'require',
        'logo'              => 'require',
    ];

    protected $message  =   [
        'title.require'             => '请输入项目名称',
        'name.require'              => '请输入项目标识',
        'name.alpha'                => '项目标识只能为字母',
        'name.unique'               => '该项目标识已创建',
        'username.require'          => '请输入管理员账号',
        'password.require'          => '请输入管理员密码',
        'app_name.require'          => '请选择关联应用',
        'logo.require'              => '请上传项目图标',
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
            ->remove('title', ['verifyTitle'])
            ->remove('name', ['unique']);
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
    
    /**
     * 项目标识验证
     * @param mixed $value
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function verifyName($value)
    {
        if (in_array($value,['admin','api','base'])) {
            return '该项目标识无法使用';
        }
        return true;
    }
}
