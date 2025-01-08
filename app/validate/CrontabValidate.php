<?php
namespace app\validate;

use taoser\Validate;

/**
 * 定时任务验证器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class CrontabValidate extends Validate
{
    protected $rule = [
        'title' => 'require',
        'command' => 'require',
        'expression' => 'require',
    ];

    protected $message = [
        'title.require' => '请输入任务名称',
        'command.require' => '请输入任务命令',
        'expression.require' => '请输入任务规则',
    ];
}
