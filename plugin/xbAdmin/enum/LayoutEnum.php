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
 * 布局模式
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class LayoutEnum extends BaseEnum
{
    const DEFAULT = [
        'label' => '默认布局',
        'value' => 'default',
        'style' => '<span class="label label-default">默认布局</span>',
    ];

    const SIDE_BAR = [
        'label' => '侧边双栏',
        'value' => 'sideBar',
        'style' => '<span class="label label-info">侧边双栏</span>',
    ];

    const USER = [
        'label' => '用户中心',
        'value' => 'user',
        'style' => '<span class="label label-success">用户中心</span>',
    ];
}