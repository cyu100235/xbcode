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
 * 菜单类型
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class MenuTypeEnum extends BaseEnum
{
    const CATALOG = [
        'label' => '目录',
        'value' => '10',
        'style' => '<span class="label label-primary">目录</span>',
    ];

    const MENU = [
        'label' => '菜单',
        'value' => '20',
        'style' => '<span class="label label-success">菜单</span>',
    ];

    const BUTTON = [
        'label' => '按钮',
        'value' => '30',
        'style' => '<span class="label label-warning">按钮</span>',
    ];
}
