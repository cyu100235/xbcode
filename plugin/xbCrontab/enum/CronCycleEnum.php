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
 * 周期类型枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CronCycleEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '每分钟',
        'value' => 'minute',
        'unit' => '分钟',
        'placeholder' => '分钟数',
    ];
    const STATE20 = [
        'label' => '每小时',
        'value' => 'hour',
        'unit' => '小时',
        'placeholder' => '小时数',
    ];
    const STATE30 = [
        'label' => '每天',
        'value' => 'day',
        'unit' => '天',
        'placeholder' => '天数',
    ];
    const STATE40 = [
        'label' => '每周',
        'value' => 'week',
        'unit' => '周',
        'placeholder' => '星期几',
    ];
    const STATE50 = [
        'label' => '每月',
        'value' => 'month',
        'unit' => '月',
        'placeholder' => '几号',
    ];
    const STATE60 = [
        'label' => '每年',
        'value' => 'year',
        'unit' => '月',
        'placeholder' => '月份',
    ];
}
