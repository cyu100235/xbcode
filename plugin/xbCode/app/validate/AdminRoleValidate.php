<?php
namespace plugin\xbCode\app\validate;

use taoser\Validate;

/**
 * 角色验证器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminRoleValidate extends Validate
{
    protected $rule = [
        'title' => 'require',
        'sort' => 'require',
    ];

    protected $message = [
        'title.require' => '请输入角色名称',
        'sort.require' => '请输入角色排序',
    ];
}
