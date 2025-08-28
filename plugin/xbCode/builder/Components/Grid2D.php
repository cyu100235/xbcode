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
 * Grid2D 二维网格布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/grid-2d
 * @method $this gridClassName(string $value) 外层 Dom 的类名
 * @method $this gap(int|string $value) 格子间距，包括水平和垂直
 * @method $this cols(int $value) 格子水平划分为几个区域
 * @method $this rowHeight(int $value) 每个格子默认垂直高度
 * @method $this rowGap(int|string $value) 格子垂直间距
 * @method $this grids(array $value) 格子集合
 */
class Grid2D extends BaseSchema
{
    public string $type = 'grid-2d';
}
