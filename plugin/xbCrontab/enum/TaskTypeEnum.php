<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 任务类型枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class TaskTypeEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '执行Shell命令',
        'value' => '10',
        'style' => '<span class="label label-warning">执行Shell命令</span>',
    ];
    const STATE20 = [
        'label' => '执行访问URL',
        'unit' =>'分钟',
        'value' => '20',
        'style' => '<span class="label label-info">访问URL</span>',
    ];
    const STATE30 = [
        'label' => '执行PHP代码',
        'unit' =>'小时',
        'value' => '30',
        'style' => '<span class="label label-success">执行PHP代码</span>',
    ];
}