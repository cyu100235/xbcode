<?php

namespace app\common\enum;

use app\common\Enum;

/**
 * 状态管理器
 *
 * @author 贵州小白基地网络科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-08
 */
class StatusEnum extends Enum
{
    const STATUS_NO = [
        'label'      => '禁用',
        'value'     => '10',
    ];
    const STATUS_YES = [
        'label'      => '正常',
        'value'     => '20',
    ];
}
