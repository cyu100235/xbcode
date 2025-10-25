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
 * AnchorNav 锚点导航渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/anchor-nav
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this linkClassName(string $value) 导航 Dom 的类名
 * @method $this sectionClassName(string $value) 锚点区域 Dom 的类名
 * @method $this links(array $value) links 内容
 * @method $this direction(string $value) 可以配置导航水平展示还是垂直展示。对应的配置项分别是：vertical、horizontal
 * @method $this active(string $value) 需要定位的区域
 */
class AnchorNav extends BaseSchema
{
    public string $type = 'anchor-nav';
}
