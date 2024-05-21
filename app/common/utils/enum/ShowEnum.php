<?php
namespace app\common\utils\enum;

use app\common\Enum;

/**
 * 显示状态枚举
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ShowEnum extends Enum
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
