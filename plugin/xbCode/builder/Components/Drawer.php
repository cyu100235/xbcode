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
 * 抽屉组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/drawer#
 * @method $this title(string $value) 设置抽屉标题
 * @method $this name(string $value) 设置抽屉名称
 * @method $this body(string $value) 设置抽屉内容
 * @method $this size(string $value) 设置抽屉大小，支持xs、sm、md、lg、xl
 * @method $this position(string $value) 设置抽屉方向，支持left、right、top、bottom
 * @method $this className(string $value) 设置抽屉最外层容器的样式类名
 * @method $this headerClassName(string $value) 设置抽屉头部区域的样式类名
 * @method $this bodyClassName(string $value) 设置抽屉内容区域的样式类名
 * @method $this footerClassName(string $value) 设置抽屉页脚区域的样式类名
 * @method $this showCloseButton(bool $value) 是否展示关闭按钮，当值为 false 时，默认开启 closeOnOutside
 * @method $this closeOnEsc(bool $value) 是否支持按 Esc 关闭抽屉
 * @method $this closeOnOutside(bool $value) 点击内容区外是否关闭抽屉
 * @method $this overlay(bool $value) 是否显示蒙层
 * @method $this resizable(bool $value) 是否可通过拖拽改变抽屉大小
 * @method $this width(string|int $value) 设置抽屉宽度，在 position 为 left 或 right 时生效
 * @method $this height(string|int $value) 设置抽屉高度，在 position 为 top 或 bottom 时生效
 * @method $this actions(array $value) 设置抽屉操作按钮
 * @method $this data(array $value) 设置抽屉数据映射，如果不设定将默认将触发按钮的上下文中继承数据
 */
class Drawer extends BaseSchema
{
    public string $type = 'drawer';
}
