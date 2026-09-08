<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\validate;

use Webman\Validation\Validator;

/**
 * 菜单（权限规则）数据验证器
 */
class AdminRuleValidate extends Validator
{
    /**
     * 验证规则
     * @var array
     */
    protected array $rules = [
        'title' => 'required|min:1|max:50',
        'pid' => 'required|integer',
        'path' => 'required|max:100',
        'method' => 'required',
        'type' => 'required|in:10,20,30',
        'is_show' => 'required|in:10,20',
        'state' => 'required|in:10,20',
        'sort' => 'integer',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected array $messages = [
        'title.required' => '请填写菜单名称',
        'title.max' => '菜单名称最多50位',
        'pid.required' => '请选择父级菜单',
        'pid.integer' => '父级菜单参数错误',
        'path.required' => '请填写路由地址',
        'path.max' => '路由地址最多100位',
        'method.required' => '至少选择一个请求类型',
        'type.required' => '请选择菜单类型',
        'type.in' => '菜单类型错误',
        'is_show.required' => '请选择是否显示',
        'is_show.in' => '是否显示值错误',
        'state.required' => '请选择启用状态',
        'state.in' => '启用状态值错误',
        'sort.integer' => '菜单排序必须是整数',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected array $scenes = [
        'add' => ['title', 'pid', 'path', 'method', 'type', 'is_show', 'state', 'sort'],
        'edit' => ['title', 'pid', 'path', 'method', 'type', 'is_show', 'state', 'sort'],
    ];
}
