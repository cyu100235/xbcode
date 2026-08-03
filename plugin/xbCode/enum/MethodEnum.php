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
 * 请求类型枚举
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class MethodEnum extends BaseEnum
{
    const GET = [
        'label' => 'GET',
        'value' => 'GET',
        'style' => '<span class="label label-success">GET</span>',
    ];
    const POST = [
        'label'=> 'POST',
        'value' => 'POST',
        'style' => '<span class="label label-info">POST</span>',
    ];
    const PUT = [
        'label'=> 'PUT',
        'value' => 'PUT',
        'style' => '<span class="label label-warning">PUT</span>',
    ];
    const DELETE = [
        'label'=> 'DELETE',
        'value' => 'DELETE',
        'style' => '<span class="label label-danger">DELETE</span>',
    ];
}