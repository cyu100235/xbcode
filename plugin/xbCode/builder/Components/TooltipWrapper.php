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
 * 文字提示容器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/tooltip
 * @method $this title(string $value) 文字提示标题
 * @method $this content(string $value) 文字提示内容, 兼容之前的 tooltip 属性
 * @method $this tooltip(string $value) 文字提示内容, 兼容之前的 tooltip 属性
 * @method $this placement(string $value) 文字提示浮层出现位置
 * @method $this offset(string $value) 文字提示浮层位置相对偏移量，单位 px
 * @method $this showArrow(string $value) 是否展示浮层指向箭头
 * @method $this enterable(string $value) 是否鼠标可以移入到浮层中
 * @method $this disabled(string $value) 是否禁用浮层提示
 * @method $this trigger(array|string $value) 浮层触发方式，支持数组写法["hover", "click"]
 * @method $this mouseEnterDelay(string $value) 浮层延迟展示时间，单位 ms
 * @method $this mouseLeaveDelay(string $value) 浮层延迟隐藏时间，单位 ms
 * @method $this rootClose(string $value) 是否点击非内容区域关闭提示
 * @method $this inline(string $value) 内容区是否内联显示
 * @method $this wrapperComponent(string $value) 容器标签名
 * @method $this body(string $value) 内容容器
 * @method $this style(string $value) 内容区自定义样式
 * @method $this tooltipStyle(string $value) 浮层自定义样式
 * @method $this className(string $value) 内容区类名
 * @method $this tooltipClassName(string $value) 文字提示浮层类名
 */
class TooltipWrapper extends BaseSchema
{
    public string $type = 'tooltip-wrapper';
}
