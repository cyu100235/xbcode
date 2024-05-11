<?php

namespace app\utils\enum;

use app\Enum;

/**
 * 封禁状态枚举
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class BanEnum extends Enum
{
    const STATUS_NO = [
        'label'      => '封禁',
        'value'     => '10',
    ];
    const STATUS_YES = [
        'label'      => '正常',
        'value'     => '20',
    ];
}
