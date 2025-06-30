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
namespace plugin\xbCode\builder\Components\Action;

use plugin\xbCode\builder\Components\Button;

/**
 * 邮件行为
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 * @method $this to(string $value) 收件人邮箱，可用 ${xxx} 取值。
 * @method $this cc(string $value) 抄送邮箱，可用 ${xxx} 取值。
 * @method $this bcc(string $value) 匿名抄送邮箱，可用 ${xxx} 取值。
 * @method $this subject(string $value) 邮件主题，可用 ${xxx} 取值。
 * @method $this body(string $value) 邮件正文，可用 ${xxx} 取值。
 */
class EmailAction extends Button
{
    public string $actionType = 'email';
}
