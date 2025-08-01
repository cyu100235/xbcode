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
 * 刷新行为
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/action
 * @method $this target(string $value) 刷新目标组件
 * @method $this icon(string $value) 图标
 * @method $this align(string $value) 对齐方式
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
 * @method $this size(string $value) 按钮大小
 * @method $this block(bool $value) 将按钮宽度调整为其父宽度的选项
 * @method $this disabled(bool $value) 是否禁用
 * @method $this visible(bool $value) 是否可见
 * @method $this className(string $value) 自定义样式类名
 * @method $this tooltip(string $value) 提示信息
 * @method $this tooltipPosition(string $value) 提示信息位置
 * @method $this tooltipTrigger(string $value) 提示信息触发方式
 * @method $this tooltipContainer(string $value) 提示信息容器
 * @method $this tooltipTheme(string $value) 提示信息主题
 * @method $this onEvent(string $event, callable $callback) 事件回调
 */
class ReloadAction extends Button
{
    public string $actionType = 'reload';
}
