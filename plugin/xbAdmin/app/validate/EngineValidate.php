<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\validate;

use Webman\Validation\Validator;

/**
 * 储存引擎验证器
 */
class EngineValidate extends Validator
{
    /**
     * 验证规则
     * @var array
     */
    protected array $rules = [
        'title' => 'required|max:50',
        'name' => 'required|max:50',
        'plugin' => 'required|max:50',
        'desc' => 'required|max:230',
        'prompt' => 'required|max:230',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected array $messages = [
        'title.required' => '引擎名称参数错误',
        'title.max' => '引擎名称字数不能超过50个字',
        'name.required' => '引擎标识参数错误',
        'name.max' => '引擎标识字数不能超过50个字',
        'plugin.required' => '引擎插件参数错误',
        'plugin.max' => '引擎插件字数不能超过50个字',
        'desc.required' => '引擎描述参数错误',
        'desc.max' => '引擎描述字数不能超过230个字',
        'prompt.required' => '引擎描述词参数错误',
        'prompt.max' => '引擎描述词字数不能超过230个字',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected array $scenes = [
        'add' => ['title', 'name', 'plugin', 'desc', 'prompt'],
        'edit' => ['title', 'name', 'plugin', 'desc', 'prompt'],
    ];
}