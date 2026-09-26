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
 * 定时任务执行方式
 *
 * php：以「类名::方法名 + JSON 参数」调用 PHP 代码，用 target、params 两个字段；
 * command：按任务类型执行 Shell 命令、访问 URL 或 PHP 代码，用 type、command 两个字段。
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ExecuteModeEnum extends BaseEnum
{
    const PHP = [
        'label' => 'PHP 可调用',
        'value' => 'php',
        'style' => '<span class="label label-primary">PHP 可调用</span>',
    ];

    const COMMAND = [
        'label' => '命令执行',
        'value' => 'command',
        'style' => '<span class="label label-warning">命令执行</span>',
    ];
}