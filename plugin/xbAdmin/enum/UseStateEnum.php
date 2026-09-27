<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbAdmin\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 使用状态枚举
 * @copyright 贵州云铺网络科技有限公司
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