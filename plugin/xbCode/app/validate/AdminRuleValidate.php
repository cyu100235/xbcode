<?php
namespace plugin\xbCode\app\validate;

use taoser\Validate;

/**
 * 菜单验证器
 * @author 贵州小白基地网络科技有限公司
 * @copyright (c) 贵州小白基地网络科技有限公司
 */
class AdminRuleValidate extends Validate
{
    protected $rule = [
        'pid' => 'require',
        'title' => 'require',
        'path' => 'require',
        'component' => 'require|verifyComponet',
        'method' => 'require',
    ];

    protected $message = [
        'pid.require' => '请选择父级菜单',
        'path.require' => '请填写路由地址',
        'title.require' => '请填写菜单名称',
        'method.require' => '请选择请求类型',
    ];

    /**
     * 添加验证场景
     * @return AdminRuleValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneAdd()
    {
        return $this->only([
            'pid',
            'title',
            'component',
            'path', 'methods'
        ]);
    }

    /**
     * 编辑验证场景
     * @return AdminRuleValidate
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function sceneEdit()
    {
        return $this->only([
            'pid',
            'title',
            'component',
            'path', 'methods'
        ]);
    }

    /**
     * 根据组件选择数据验证
     * @param mixed $value
     * @param mixed $rule
     * @param mixed $data
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @Email 958416459@qq.com
     * @DateTime 2023-04-30
     */
    public function verifyComponet($value, $rule, $data)
    {
        // 远程组件
        if ($data['component'] === 'remote/index') {
            if (!isset($data['remote']) || !$data['remote']) {
                return '请输入远程组件地址';
            }
        }
        return true;
    }
}
