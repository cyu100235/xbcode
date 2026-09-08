<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 是/否枚举，用于 is_system、is_default 等开关字段
 */
class YesEnum extends BaseEnum
{
    const NO = [
        'label' => '否',
        'value' => '10',
        'style' => '<span class="label label-warning">否</span>',
    ];

    const YES = [
        'label' => '是',
        'value' => '20',
        'style' => '<span class="label label-success">是</span>',
    ];
}
