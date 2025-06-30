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
 * 分割线组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/divider
 * @method $this className(string $value) 设置外层 Dom 的类名
 * @method $this lineStyle(string $value) 设置分割线的样式，支持dashed和solid
 * @method $this direction(string $value) 设置分割线的方向，支持horizontal和vertical
 * @method $this color(string $value) 设置分割线的颜色
 * @method $this rotate(int $value) 设置分割线的旋转角度
 * @method $this title(SchemaNode $value) 设置分割线的标题
 * @method $this titleClassName(string $value) 设置分割线的标题类名
 * @method $this titlePosition(string $value) 设置分割线的标题位置，支持left、center和right
 */
class Divider extends BaseSchema
{
    public string $type = 'divider';
}
