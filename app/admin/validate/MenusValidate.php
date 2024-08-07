<?php
namespace app\admin\validate;

use Tinywan\Validate\Validate;

/**
 * 菜单验证器
 * @author 贵州小白基地网络科技有限公司
 * @copyright (c) 贵州小白基地网络科技有限公司
 */
class MenusValidate extends Validate
{
    protected array $rule = [
        'pid' => 'require',
        'title' => 'require',
        'path' => 'require',
        'component' => 'require|verifyComponet',
        'methods' => 'require',
    ];

    protected array $message = [
        'pid.require' => '请选择父级菜单',
        'path.require' => '请输入路由地址',
        'title.require' => '请输入菜单名称',
        'methods.require' => '请选择请求类型',
    ];

    protected array $scene = [
        'add' => [
            'pid',
            'title',
            'component',
            'path',
            'methods',
        ],
        'edit' => [
            'pid',
            'title',
            'component',
            'path',
            'methods',
        ],
    ];

    /**
     * 根据组件选择数据验证
     * @param mixed $value
     * @param mixed $rule
     * @param mixed $data
     * @return bool|string
     * @copyright 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function verifyComponet($value, $rule, $data)
    {
        // 远程组件
        if ($data['component'] === 'remote/index') {
            if (!isset($data['params']) || !$data['params']) {
                return '请输入远程组件地址';
            }
        }
        return true;
    }
}
