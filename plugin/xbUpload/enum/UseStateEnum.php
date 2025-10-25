<?php
namespace plugin\xbUpload\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 使用状态枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class UseStateEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '未使用',
        'value' => '10',
        'style' => '<span class="label label-warning">未使用</span>',
    ];
    const STATE20 = [
        'label' => '使用中',
        'value' => '20',
        'style' => '<span class="label label-success">使用中</span>',
    ];
}