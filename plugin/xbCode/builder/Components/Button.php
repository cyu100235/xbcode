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
 * Button 按钮渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/button
 * @method $this id(string $value) 按钮ID
 * @method $this label(string $value) 按钮文本
 * @method $this icon(string $value) 按钮图标
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this url(string $value) 点击跳转的地址，指定此属性 button 的行为和 a 链接一致
 * @method $this size(string $value) 设置按钮大小
 * @method $this actionType(string $value) 设置按钮类型
 * @method $this level(string $value) 设置按钮样式
 * @method $this tooltip(string $value) 气泡提示内容
 * @method $this tooltipPlacement(string $value) 气泡框位置器
 * @method $this tooltipTrigger(string $value) 触发 tooltip
 * @method $this disabled(bool $value) 按钮失效状态
 * @method $this block(bool $value) 将按钮宽度调整为其父宽度的选项
 * @method $this loading(bool $value) 显示按钮 loading 效果
 * @method $this loadingOn(string $value) 显示按钮 loading 表达式
 * @method $this confirmText(string $value) 确认框消息文本
 * @method $this confirmLevel(string $value) 确认按钮样式
 * @method $this confirmType(string $value) 确认按钮类型
 */
class Button extends BaseSchema
{
    public string $type = 'button';
}
