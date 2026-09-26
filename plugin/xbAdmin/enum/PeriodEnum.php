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
 * 定时任务执行周期
 *
 * 周期为固定间隔，按「大单位 → 小单位」逐级细化，不支持自定义：
 * 每X秒 / 每X分钟X秒 / 每X小时X分X秒 / 每X天X时X分X秒 / 每X周X日X时X分X秒 / 每X月X日X时X分X秒。
 * 各单位的取值上限与换算见 model\Crontab 的 UNITS、UNIT_SECONDS 常量。
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PeriodEnum extends BaseEnum
{
    const SECOND = [
        'label' => '每X秒',
        'value' => 'second',
        'style' => '<span class="label label-default">每X秒</span>',
    ];

    const MINUTE = [
        'label' => '每X分钟X秒',
        'value' => 'minute',
        'style' => '<span class="label label-info">每X分钟X秒</span>',
    ];

    const HOUR = [
        'label' => '每X小时X分X秒',
        'value' => 'hour',
        'style' => '<span class="label label-info">每X小时X分X秒</span>',
    ];

    const DAY = [
        'label' => '每X天X时X分X秒',
        'value' => 'day',
        'style' => '<span class="label label-primary">每X天X时X分X秒</span>',
    ];

    const WEEK = [
        'label' => '每X周X日X时X分X秒',
        'value' => 'week',
        'style' => '<span class="label label-primary">每X周X日X时X分X秒</span>',
    ];

    const MONTH = [
        'label' => '每X月X日X时X分X秒',
        'value' => 'month',
        'style' => '<span class="label label-primary">每X月X日X时X分X秒</span>',
    ];
}