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
 * 操作日志类型
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class LogActionEnum extends BaseEnum
{
    const ADD = [
        'label' => '新增',
        'value' => '10',
        'style' => '<span class="label label-success">新增</span>',
    ];
    const EDIT = [
        'label' => '修改',
        'value' => '20',
        'style' => '<span class="label label-warning">修改</span>',
    ];
    const DELETE = [
        'label' => '删除',
        'value' => '30',
        'style' => '<span class="label label-danger">删除</span>',
    ];
    const LOGIN = [
        'label' => '登录',
        'value' => '40',
        'style' => '<span class="label label-info">登录</span>',
    ];
}