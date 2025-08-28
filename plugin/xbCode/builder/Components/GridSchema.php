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
 * 宫格布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/grid
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this columns(array $value) 列集合
 * @method $this gap(string $value) 水平间距
 * @method $this valign(string $value) 垂直对齐方式
 * @method $this align(string $value) 水平对齐方式
 */
class GridSchema extends BaseSchema
{
    public string $type = 'grid';
}
