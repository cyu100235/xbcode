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
 * 菜单背景预设
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class SideColorEnum extends BaseEnum
{
    const WHITE = [
        'label' => '白色',
        'value' => '#ffffff',
        'style' => '<span class="label" style="background:#ffffff;color:#333">白色</span>',
    ];

    const DARK_BLACK = [
        'label' => '深黑',
        'value' => '#151b26',
        'style' => '<span class="label" style="background:#151b26;color:#fff">深黑</span>',
    ];

    const DARK_GRAY = [
        'label' => '深灰',
        'value' => '#2f3542',
        'style' => '<span class="label" style="background:#2f3542;color:#fff">深灰</span>',
    ];

    const ORANGE = [
        'label' => '橙色',
        'value' => '#ff6a00',
        'style' => '<span class="label" style="background:#ff6a00;color:#fff">橙色</span>',
    ];

    const BLUE = [
        'label' => '蓝色',
        'value' => '#2468f2',
        'style' => '<span class="label" style="background:#2468f2;color:#fff">蓝色</span>',
    ];

    const GREEN = [
        'label' => '绿色',
        'value' => '#0bc286',
        'style' => '<span class="label" style="background:#0bc286;color:#fff">绿色</span>',
    ];

    const CYAN = [
        'label' => '青色',
        'value' => '#13c2c2',
        'style' => '<span class="label" style="background:#13c2c2;color:#fff">青色</span>',
    ];

    const PURPLE = [
        'label' => '紫色',
        'value' => '#722ed1',
        'style' => '<span class="label" style="background:#722ed1;color:#fff">紫色</span>',
    ];

    const RED = [
        'label' => '红色',
        'value' => '#f23d3d',
        'style' => '<span class="label" style="background:#f23d3d;color:#fff">红色</span>',
    ];
}