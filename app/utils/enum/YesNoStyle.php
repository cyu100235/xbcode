<?php
namespace app\utils\enum;

use app\Enum;

/**
 * YES/NO 枚举类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class YesNoStyle extends Enum
{
    const STATUS_NO = [
        'label'      => 'danger',
        'value'     => '10',
    ];
    const STATUS_YES = [
        'label'      => 'success',
        'value'     => '20',
    ];
}
