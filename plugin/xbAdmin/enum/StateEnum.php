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
 * 是否启用
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class StateEnum extends BaseEnum
{
    const DISABLED = [
        'label' => '禁用',
        'value' => '10',
        'style' => '<span class="label label-danger">禁用</span>',
    ];

    const ENABLED = [
        'label' => '启用',
        'value' => '20',
        'style' => '<span class="label label-success">启用</span>',
    ];
}
