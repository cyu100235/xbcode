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
 * 任务周期枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class TaskCycleEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '每 N 秒',
        'unit' =>'秒',
        'value' => '10',
        'style' => '<span class="label label-warning">每 N 秒</span>',
    ];
    const STATE20 = [
        'label' => '每 N 分钟',
        'unit' =>'分钟',
        'value' => '20',
        'style' => '<span class="label label-success">每 N 分钟</span>',
    ];
    const STATE30 = [
        'label' => '每 N 小时',
        'unit' =>'小时',
        'value' => '30',
        'style' => '<span class="label label-success">每 N 小时</span>',
    ];
    const STATE40 = [
        'label' => '每 N 天',
        'unit' =>'天',
        'value' => '40',
        'style' => '<span class="label label-success">每 N 天</span>',
    ];
    const STATE50 = [
        'label' => '每 N 周',
        'unit' =>'周',
        'value' => '50',
        'style' => '<span class="label label-success">每 N 周</span>',
    ];
    const STATE60 = [
        'label' => '每 N 月',
        'unit' =>'月',
        'value' => '60',
        'style' => '<span class="label label-success">每 N 月</span>',
    ];
    const STATE70 = [
        'label' => '每 N 年',
        'unit' =>'年',
        'value' => '70',
        'style' => '<span class="label label-success">每 N 年</span>',
    ];
}