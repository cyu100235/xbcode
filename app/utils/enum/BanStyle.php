<?php

namespace app\utils\enum;

use app\Enum;

/**
 * 封禁状态样式
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class BanStyle extends Enum
{
    const STATUS_NO = [
        'label'      => 'error',
        'value'     => '10',
    ];
    const STATUS_YES = [
        'label'      => 'success',
        'value'     => '20',
    ];
}
