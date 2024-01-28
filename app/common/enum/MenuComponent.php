<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 权限规则-组件类型 枚举类
 * @author 贵州小白基地网络科技有限公司
 * @copyright 贵州小白基地网络科技有限公司
 * @email 416716328@qq.com
 */
class MenuComponent extends Enum
{
    const DIR_VIEW = [
        'label'      => '不使用组件',
        'value'     => 'none/index',
    ];
    const FORM_VIEW = [
        'label'      => '表单组件',
        'value'     => 'form/index',
    ];
    const TABLE_VIEW = [
        'label'      => '表格组件',
        'value'     => 'table/index',
    ];
    const REMOTE_VIEW = [
        'label'      => '远程Vue组件',
        'value'     => 'remote/index',
    ];
    const VUE_VIEW = [
        'label'      => '渲染Vue组件',
        'value'     => 'vue/index',
    ];
}
