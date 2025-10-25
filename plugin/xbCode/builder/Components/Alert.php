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
namespace plugin\xbCode\builder\Components;

/**
 * Alert 提示渲染器。
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/alert
 * @method $this title(string $value) alert标题
 * @method $this body(string $value) 内容
 * @method $this level(string $value) 提示类型
 * @method $this showCloseButton(bool $value) 是否显示关闭按钮
 * @method $this closeButtonClassName(string $value) 关闭按钮类名
 * @method $this showIcon(bool $value) 是否显示图标
 * @method $this icon(string $value) 图标
 * @method $this iconClassName(string $value) 图标类名
 * @method $this className(string $value) 自定义类名
 */
class Alert extends BaseSchema
{
    public string $type = 'alert';
}
