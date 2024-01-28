<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 显示状态通用枚举
 * @author 贵州小白基地网络科技有限公司
 * @copyright 贵州小白基地网络科技有限公司
 * @email 416716328@qq.com
 */
class ShowStatus extends Enum
{
    const SHOW_NO = [
        'label'      => '隐藏',
        'value'     => '10',
    ];
    const SHOW_YES = [
        'label'      => '显示',
        'value'     => '20',
    ];
}
