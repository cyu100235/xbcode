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
namespace plugin\xbCode\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 菜单类型枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class MenuTypeEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '目录',
        'value' => '10',
        'style' => '<span class="label label-primary">目录</span>',
    ];
    const STATE20 = [
        'label' => '菜单',
        'value' => '20',
        'style' => '<span class="label label-success">菜单</span>',
    ];
    const MENU_TO_30 = [
        'label' => '按钮',
        'value' => '30',
        'style' => '<span class="label label-warning">按钮</span>',
    ];
}