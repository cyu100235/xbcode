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
 * 标签组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/tag
 * @method $this className(string $value) 外层 Dom 的 CSS 类名
 * @method $this style(string $value) 外层 Dom 的内联样式
 * @method $this color(string $value) 颜色
 * @method $this label(string $value) 标签内容
 * @method $this displayMode(string $value) 显示模式
 * @method $this icon(string $value) 图标
 * @method $this closable(string $value) 是否可关闭
 * @method $this closeIcon(string $value) 关闭图标
 * @method $this checkable(string $value) 是否可选中
 * @method $this checked(string $value) 是否选中
 * @method $this disabled(string $value) 是否禁用
 * @method $this click(mixed $value) 点击事件
 * @method $this mouseenter(mixed $value) 鼠标移入触发
 * @method $this mouseleave(mixed $value) 鼠标移出触发
 * @method $this close(mixed $value) 关闭事件
 */
class Tag extends BaseSchema
{
    public string $type = 'tag';
}
