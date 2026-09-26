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
 * 主题模式
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ThemeEnum extends BaseEnum
{
    const OS = [
        'label' => '跟随系统',
        'value' => 'OS',
        'style' => '<span class="label label-success">跟随系统</span>',
    ];
    const LIGHT = [
        'label' => '浅色模式',
        'value' => 'light',
        'style' => '<span class="label label-default">浅色模式</span>',
    ];

    const DARK = [
        'label' => '深色模式',
        'value' => 'dark',
        'style' => '<span class="label label-info">深色模式</span>',
    ];
}