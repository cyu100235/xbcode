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
        'title' => 'require',
        'pid' => 'require',
        'path' => 'require',
        'method' => 'require',
    ];

    protected $message = [
        'title.require' => '请填写菜单名称',
        'pid.require' => '请选择父级菜单',
        'path.require' => '请填写路由地址',
        'method.require' => '至少选择一个请求类型',
    ];
}
