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
 * 下拉按钮组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/dropdown-button
 * @method $this label(string $value) 设置按钮文本
 * @method $this className(string $value) 设置外层 CSS 类名
 * @method $this btnClassName(string $value) 设置按钮 CSS 类名
 * @method $this menuClassName(string $value) 设置下拉菜单 CSS 类名
 * @method $this block(bool $value) 设置块状样式
 * @method $this size(string $value) 设置尺寸，支持'xs'、'sm'、'md' 、'lg'
 * @method $this align(string $value) 设置位置，可选'left'或'right'
 * @method $this buttons(array $value) 设置下拉按钮
 * @method $this iconOnly(bool $value) 设置只显示 icon
 * @method $this defaultIsOpened(bool $value) 设置默认是否打开
 * @method $this closeOnOutside(bool $value) 设置点击外侧区域是否收起
 * @method $this closeOnClick(bool $value) 设置点击按钮后自动关闭下拉菜单
 * @method $this trigger(string $value) 设置触发方式
 * @method $this hideCaret(bool $value) 设置隐藏下拉图标
 */
class DropdownButton extends BaseSchema
{
    public string $type = 'dropdown-button';
}
