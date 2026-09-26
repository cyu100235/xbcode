<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\validate;

use Webman\Validation\Validator;

/**
 * 字典数据验证器
 */
class DictValidate extends Validator
{
    /**
     * 验证规则
     * @var array
     */
    protected array $rules = [
        'plugin' => 'required|max:50',
        'name' => 'required|max:50',
        'title' => 'required|max:50',
        'sort' => 'integer',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected array $messages = [
        'plugin.required' => '请填写插件标识',
        'plugin.max' => '插件标识最多50位',
        'name.required' => '请填写枚举标识',
        'name.max' => '枚举标识最多50位',
        'title.required' => '请填写枚举标题',
        'title.max' => '枚举标题最多50位',
        'sort.integer' => '排序必须是整数',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected array $scenes = [
        'add' => ['plugin', 'name', 'title', 'sort'],
        'edit' => ['plugin', 'name', 'title', 'sort'],
    ];
}