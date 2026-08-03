<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 颜色枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ColorsEnum extends BaseEnum
{
    const PURPLE = [
        'label' => '紫色',
        'value' => '#7B64FF',
        'style' => '<span class="label label-permary">紫色</span>',
    ];
    const RED = [
        'label' => '红色',
        'value' => '#F44E3B',
        'style' => '<span class="label label-permary">红色</span>',
    ];
    const PINK = [
        'label'=> '粉色',
        'value' => '#f562b3ff',
        'style' => '<span class="label label-permary">粉色</span>',
    ];
    const GREEN = [
        'label' => '绿色',
        'value' => '#68BC00',
        'style' => '<span class="label label-permary">绿色</span>',
    ];
    const YELLOW = [
        'label' => '黄色',
        'value' => '#FB9E00',
        'style' => '<span class="label label-permary">黄色</span>',
    ];
    const BLUE = [
        'label' => '蓝色',
        'value' => '#16A5A5',
        'style' => '<span class="label label-permary">蓝色</span>',
    ];
    const BLACK = [
        'label' => '黑色',
        'value' => '#000000',
        'style' => '<span class="label label-permary">黑色</span>',
    ];
}