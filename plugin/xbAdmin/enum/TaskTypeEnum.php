<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 定时任务类型
 *
 * 仅「执行方式」为命令执行时生效：10 执行 Shell 命令、20 访问 URL、30 执行 PHP 代码，
 * 分别对应进程里 shell_exec、file_get_contents、eval 三种执行分支。
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class TaskTypeEnum extends BaseEnum
{
    const SHELL = [
        'label' => '执行Shell命令',
        'value' => '10',
        'style' => '<span class="label label-warning">执行Shell命令</span>',
    ];

    const URL = [
        'label' => '访问URL',
        'value' => '20',
        'style' => '<span class="label label-info">访问URL</span>',
    ];

    const PHP = [
        'label' => '执行PHP代码',
        'value' => '30',
        'style' => '<span class="label label-success">执行PHP代码</span>',
    ];
}