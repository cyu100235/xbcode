<?php
namespace app\common\validate;

use think\Validate;

/**
 * 菜单验证器
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class MenusValidate extends Validate
{
    protected $rule = [
        'title'         => 'require',
        'pid'           => 'require',
        'component'     => 'require|verifyComponet',
        'path'          => 'require|verifyPath',
        'method'        => 'require',
    ];

    protected $message = [
        'title.require'     => '请输入菜单名称',
        'pid.require'       => '请选择父级菜单',
        'path.require'      => '请输入菜单路由',
        'method.require'    => '请选择请求类型',
    ];

    protected $scene = [
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
     * 验证菜单路由
     * @param mixed $value
     * @param mixed $rule
     * @param mixed $data
     * @return bool|string
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function verifyPath($value, $rule, $data)
    {
        # 判断菜单路由不存在斜杠
        if (strpos($value, '/') === false) {
            return '请输入正确的菜单路由';
        }
        return true;
    }
    
    /**
     * 根据组件选择数据验证
     * @param mixed $value
     * @param mixed $rule
     * @param mixed $data
     * @return bool|string
     * @copyright 贵州猿创科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-04-30
     */
    public function verifyComponet($value, $rule, $data)
    {
        // 远程组件
        if ($data['component'] === 'remote/index') {
            if (!isset($data['auth_params']) || !$data['auth_params']) {
                return '请输入远程组件地址';
            }
        }
        return true;
    }
}
