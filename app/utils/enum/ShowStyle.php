<?php
namespace app\utils\enum;

use app\Enum;

/**
 * 显示样式类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ShowStyle extends Enum
{
    const SHOW_NO = [
        'label'      => 'danger',
        'value'     => '10',
    ];
    const SHOW_YES = [
        'label'      => 'success',
        'value'     => '20',
    ];
}
