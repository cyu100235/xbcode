<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\validate;

use Webman\Validation\Validator;

/**
 * 角色数据验证器
 */
class AdminRoleValidate extends Validator
{
    /**
     * 验证规则
     * @var array
     */
    protected array $rules = [
        'title' => 'required|min:2|max:50',
        'sort' => 'required|integer',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected array $messages = [
        'title.required' => '请输入角色名称',
        'title.min' => '角色名称至少2位',
        'title.max' => '角色名称最多50位',
        'sort.required' => '请输入角色排序',
        'sort.integer' => '角色排序必须是整数',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected array $scenes = [
        'add' => ['title', 'sort'],
        'edit' => ['title', 'sort'],
    ];
}
