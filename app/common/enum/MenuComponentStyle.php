<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 权限规则-组件类型样式 枚举类
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class MenuComponentStyle extends Enum
{
    const DIR_VIEW = [
        'label'      => '',
        'value'     => 'none/index',
    ];
    const FORM_VIEW = [
        'label'      => 'warning',
        'value'     => 'form/index',
    ];
    const TABLE_VIEW = [
        'label'      => 'success',
        'value'     => 'table/index',
    ];
    const REMOTE_VIEW = [
        'label'      => 'info',
        'value'     => 'remote/index',
    ];
}
