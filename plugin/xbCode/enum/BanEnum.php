<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 封禁状态枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class BanEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '已封禁',
        'value' => '10',
        'style' => '<span class="label label-warning">已封禁</span>',
    ];
    const STATE20 = [
        'label' => '未封禁',
        'value' => '20',
        'style' => '<span class="label label-success">未封禁</span>',
    ];
}