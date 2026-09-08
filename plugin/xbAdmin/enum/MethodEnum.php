<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\enum;

use plugin\xbCode\base\BaseEnum;

/**
 * 请求类型枚举
 */
class MethodEnum extends BaseEnum
{
    const GET = [
        'label' => 'GET',
        'value' => 'GET',
        'style' => '<span class="label label-success">GET</span>',
    ];

    const POST = [
        'label' => 'POST',
        'value' => 'POST',
        'style' => '<span class="label label-info">POST</span>',
    ];

    const PUT = [
        'label' => 'PUT',
        'value' => 'PUT',
        'style' => '<span class="label label-warning">PUT</span>',
    ];

    const DELETE = [
        'label' => 'DELETE',
        'value' => 'DELETE',
        'style' => '<span class="label label-danger">DELETE</span>',
    ];
}
