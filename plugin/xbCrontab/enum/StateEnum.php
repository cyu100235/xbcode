<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 任务状态
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class StateEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '未运行',
        'value' => '10',
        'style' => '<span class="label label-danger">未运行</span>',
    ];
    const STATE20 = [
        'label'=> '运行中',
        'value' => '20',
        'style' => '<span class="label label-success">运行中</span>',
    ];
    const STATE30 = [
        'label'=> '已停止',
        'value' => '30',
        'style' => '<span class="label label-danger">已停止</span>',
    ];
}