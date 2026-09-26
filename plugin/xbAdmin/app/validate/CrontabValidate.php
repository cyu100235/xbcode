<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\validate;

use Webman\Validation\Validator;

/**
 * 定时任务数据验证器
 *
 * 校验控制器合成后的入库字段：周期选择已在控制器中转换为 rule，故此处校验 rule 而非 period；
 * 执行方式相关的必填项（执行目标、执行参数、任务命令）由控制器按 mode 分支判定，此处只限制长度。
 */
class CrontabValidate extends Validator
{
    /**
     * 验证规则
     * @var array
     */
    protected array $rules = [
        'title' => 'required|max:50',
        'plugin' => 'max:100',
        'name' => 'max:50',
        'mode' => 'required|in:php,command',
        'type' => 'required|in:10,20,30',
        'target' => 'max:255',
        'command' => 'max:255',
        'rule' => 'required|max:100',
        'state' => 'required|in:10,20',
        'remark' => 'max:255',
    ];

    /**
     * 错误提示
     * @var array
     */
    protected array $messages = [
        'title.required' => '请填写任务名称',
        'title.max' => '任务名称最多50位',
        'plugin.max' => '所属插件最多100位',
        'name.max' => '任务标识最多50位',
        'mode.required' => '请选择执行方式',
        'mode.in' => '执行方式错误',
        'type.required' => '请选择任务类型',
        'type.in' => '任务类型错误',
        'target.max' => '执行目标最多255位',
        'command.max' => '任务命令最多255位',
        'rule.required' => '请设置调度规则',
        'rule.max' => '调度规则最多100位',
        'state.required' => '请选择任务状态',
        'state.in' => '任务状态错误',
        'remark.max' => '备注最多255位',
    ];

    /**
     * 验证场景
     * @var array
     */
    protected array $scenes = [
        'add' => ['title', 'plugin', 'name', 'mode', 'type', 'target', 'command', 'rule', 'state', 'remark'],
        'edit' => ['title', 'plugin', 'name', 'mode', 'type', 'target', 'command', 'rule', 'state', 'remark'],
    ];
}