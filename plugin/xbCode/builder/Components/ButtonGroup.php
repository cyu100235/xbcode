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
 * 按钮组渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/button-group
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this btnClassName(string $value) 按钮类名
 * @method $this btnActiveClassName(string $value) 选中按钮类名
 * @method $this btnLevel(string $value) 按钮样式
 * @method $this btnActiveLevel(string $value) 选中按钮样式
 * @method $this buttons(array $value) 按钮
 * @method $this vertical(bool $value) 是否使用垂直模式
 * @method $this tiled(bool $value) 是否使用平铺模式
 * @method $this disabled(bool $value) 是否禁用
 * @method $this visible(bool $value) 是否可见
 * @method $this visibleOn(string $value) 是否可见表达式
 * @method $this size(string $value) 按钮大小
 */
class ButtonGroup extends BaseSchema
{
    public string $type = 'button-group';
}
