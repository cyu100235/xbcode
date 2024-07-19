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
        'title'         => 'require',
        'pid'           => 'require',
        'component'     => 'require|verifyComponet',
        'path'          => 'require',
        'method'        => 'require',
    ];

    protected array $message = [
        'title.require'     => '请输入菜单名称',
        'pid.require'       => '请选择父级菜单',
        'path.require'      => '请输入菜单路由',
        'method.require'    => '请选择请求类型',
    ];

    protected array $scene = [
        'add'  => [
            'pid',
            'title',
            'component',
            'path',
        ],
        'edit' => [
            'pid',
            'title',
            'component',
            'path',
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
