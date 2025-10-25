<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 请求类型枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ShowEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '隐藏',
        'value' => '10',
        'style' => '<span class="label label-warning">隐藏</span>',
    ];
    const STATE20 = [
        'label'=> '显示',
        'value' => '20',
        'style' => '<span class="label label-success">显示</span>',
    ];
}