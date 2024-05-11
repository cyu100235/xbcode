<?php

namespace app\utils\enum;

use app\Enum;

/**
 * 请求类型枚举
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MethodsEnum extends Enum
{
    const GET = [
        'label'      => 'GET',
        'value'     => 'GET',
    ];
    const POST = [
        'label'      => 'POST',
        'value'     => 'POST',
    ];
    const PUT = [
        'label'      => 'PUT',
        'value'     => 'PUT',
    ];
    const DELETE = [
        'label'      => 'DELETE',
        'value'     => 'DELETE',
    ];
}
