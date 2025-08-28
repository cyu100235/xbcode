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
 * @method $this to(string $value) 收件人邮箱，可用 ${xxx} 取值
 * @method $this cc(string $value) 抄送邮箱，可用 ${xxx} 取值
 * @method $this bcc(string $value) 匿名抄送邮箱，可用 ${xxx} 取值
 * @method $this subject(string $value) 邮件主题，可用 ${xxx} 取值
 * @method $this body(string $value) 邮件正文，可用 ${xxx} 取值
 * @method $this block(bool $value) 将按钮宽度调整为其父宽度的选项
 * @method $this level(string $value) 按钮样式
 * - `link` - 链接样式
 * - `primary` - 主要按钮样式
 * - `enhance` - 增强按钮样式
 * - `secondary` - 次要按钮样式
 * - `info` - 信息按钮样式
 * - `success` - 成功按钮样式
 * - `warning` - 警告按钮样式
 * - `danger` - 危险按钮样式
 * - `light` - 浅色按钮样式
 * - `dark` - 深色按钮样式
 * - `default` - 默认按钮样式
 */
class EmailAction extends Button
{
    public string $actionType = 'email';
}
