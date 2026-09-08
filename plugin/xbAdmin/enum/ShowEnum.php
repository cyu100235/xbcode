<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 菜单显示状态枚举
 */
class ShowEnum extends BaseEnum
{
    const HIDE = [
        'label' => '隐藏',
        'value' => '10',
        'style' => '<span class="label label-warning">隐藏</span>',
    ];

    const SHOW = [
        'label' => '显示',
        'value' => '20',
        'style' => '<span class="label label-success">显示</span>',
    ];
}
