<?php
namespace app\common\enum;

use app\common\Enum;

/**
 * 应用平台类型
 * 支持类型：'wechat','mini_wechat','douyin','h5','app','other'
 * @author 贵州小白基地网络科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-03-22
 */
class PlatformTypesStyle extends Enum
{
    const WECHAT = [
        'label'      => 'success',
        'value'     => 'wechat'
    ];
    const MINI_WECHAT = [
        'label'      => 'success',
        'value'     => 'mini_wechat'
    ];
    const DOUYIN = [
        'label'      => 'info',
        'value'     => 'douyin'
    ];
    const H5 = [
        'label'      => 'error',
        'value'     => 'h5'
    ];
    const APP = [
        'label'      => 'error',
        'value'     => 'app'
    ];
    const OTHER = [
        'label'      => 'warning',
        'value'     => 'other'
    ];
}
