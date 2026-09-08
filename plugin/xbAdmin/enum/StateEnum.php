<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 启用状态枚举
 */
class StateEnum extends BaseEnum
{
    const DISABLED = [
        'label' => '禁用',
        'value' => '10',
        'style' => '<span class="label label-danger">禁用</span>',
    ];

    const ENABLED = [
        'label' => '启用',
        'value' => '20',
        'style' => '<span class="label label-success">启用</span>',
    ];
}
