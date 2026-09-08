<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 菜单类型枚举
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
