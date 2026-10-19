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
 * 操作日志结果
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class LogResultEnum extends BaseEnum
{
    const FAIL = [
        'label' => '失败',
        'value' => '10',
        'style' => '<span class="label label-danger">失败</span>',
    ];
    const SUCCESS = [
        'label' => '成功',
        'value' => '20',
        'style' => '<span class="label label-success">成功</span>',
    ];
}