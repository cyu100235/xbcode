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
 * 是否显示
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ShowEnum extends BaseEnum
{
    const HIDE = [
        'label' => '隐藏',
        'value' => '10',
        'style' => '<span class="label label-warning">隐藏</span>',
    ];

    const SHOW = [
        'label' => '显示',
        'value' => '20',
        'style' => '<span class="label label-success">显示</span>',
    ];
}
