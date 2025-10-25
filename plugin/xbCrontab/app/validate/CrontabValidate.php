<?php
/**
 * 贵州猿创科技有限公司
 *
 * @package  XhAdmin
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xhadmin.cn
 * @document http://doc.xhadmin.cn
 */
namespace plugin\xbCrontab\app\validate;

use taoser\Validate;

/**
 * 定时任务验证器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabValidate extends Validate
{
    protected $rule = [
        'title' => 'require',
        'plugin' => 'require',
        'type' => 'require',
        'unit' => 'require',
        'exec_date' => 'require',
        'command' => 'require',
    ];

    protected $message = [
        'title.require' => '请输入定时任务名称',
        'plugin.require' => '请输入插件名称',
        'type.require' => '请输入定时任务类型',
        'unit.require' => '请输入定时任务单位',
        'exec_date.require' => '请输入定时任务执行时间',
        'command.require' => '请输入定时任务命令',
    ];
}
