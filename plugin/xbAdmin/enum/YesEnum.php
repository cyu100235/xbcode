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
class YesEnum extends BaseEnum
{
    const NO = [
        'label' => '否',
        'value' => '10',
        'style' => '<span class="label label-warning">否</span>',
    ];

    const YES = [
        'label' => '是',
        'value' => '20',
        'style' => '<span class="label label-success">是</span>',
    ];
}
