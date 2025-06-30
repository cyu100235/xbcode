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
 * 弹窗行为
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 * @method $this id(string $value) 按钮ID
 * @method $this icon(string $value) 按钮图标
 * @method $this label(string $value) 按钮文本
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this url(string $value) 点击跳转的地址，指定此属性 button 的行为和 a 链接一致
 * @method $this size(string $value) 弹窗尺寸，支持: xs、sm、md、lg、xl、full
 * @method $this actionType(string $value) 设置按钮类型
 * @method $this level(string $value) 设置按钮样式
 * @method $this tooltip(string $value) 气泡提示内容
 * @method $this tooltipPlacement(string $value) 气泡框位置器
 * @method $this tooltipTrigger(string $value) 触发 tooltip
 * @method $this disabled(bool $value) 按钮失效状态
 * @method $this disabledTip(string $value) 按钮失效提示
 * @method $this block(bool $value) 将按钮宽度调整为其父宽度的选项
 * @method $this loading(bool $value) 显示按钮 loading 效果
 * @method $this loadingOn(string $value) 显示按钮 loading 表达式
 * @method $this confirmText(string $value) 确认按钮文本
 * @method $this confirmLevel(string $value) 确认按钮样式
 * @method $this confirmType(string $value) 确认按钮类型
 * @method $this dialog(mixed $dialog) 弹框内容，格式可参考Dialog组件
 * @method $this nextCondition(mixed $value) 可以用来设置下一条数据的条件，默认为 true
 * @method $this reload(mixed $value) 是否刷新当前页面
 * @method $this redirect(mixed $value) 是否跳转到指定页面
 */
class DialogAction extends Button
{
    public string $actionType = 'dialog';
}
