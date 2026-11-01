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
 * 登录模式枚举
 * 与角色表的 is_single_login 字段取值一致：10未开启单点登录（允许多端），20已开启单点登录
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class LoginModeEnum extends BaseEnum
{
    const STATE10 = [
        'label' => '多端登录',
        'value' => '10',
        'style' => '<span class="label label-info">多端登录</span>',
    ];
    const STATE20 = [
        'label' => '单点登录',
        'value' => '20',
        'style' => '<span class="label label-success">单点登录</span>',
    ];
}
