<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 开关枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class SwitchEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '关闭',
        'value' => '10',
        'style' => '<span class="label label-danger">关闭</span>',
    ];
    const STATE20 = [
        'label' => '开启',
        'value' => '20',
        'style' => '<span class="label label-success">开启</span>',
    ];
}