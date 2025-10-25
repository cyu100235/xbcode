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
 * Color 颜色选择器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/color
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this value(string $value) 显示的颜色值
 * @method $this name(string $value) 在其他组件中，时，用作变量映射
 * @method $this popOverContainerSelector(string $value) 弹层挂载位置选择器，会通过querySelector获取
 * @method $this showValue(string $value) 是否显示右边的颜色值
 * @method $this defaultColor(string $value) 默认颜色值
 * @method $this popOverContainer(string $value) 弹层挂载位置，会通过querySelector获取
 */
class Color extends BaseSchema
{
    public string $type = 'color';
}
