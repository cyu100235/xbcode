<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 请求类型
 *
 * @author 贵州小白基地网络科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-07
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
