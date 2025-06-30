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
 * 宫格导航
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/grid-nav
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this contentClassName(string $value) 列表项内容 css 类名
 * @method $this value(array $value) 图片数组
 * @method $this source(string $value) 数据源
 * @method $this square(bool $value) 是否将列表项固定为正方形
 * @method $this center(bool $value) 是否将列表项内容居中显示
 * @method $this border(bool $value) 是否显示列表项边框
 * @method $this gutter(int $value) 列表项之间的间距，默认单位为px
 * @method $this reverse(bool $value) 是否调换图标和文本的位置
 * @method $this iconRatio(int $value) 图标宽度占比，单位%
 * @method $this direction(string $value) 列表项内容排列的方向，可选值为 horizontal 、vertical
 * @method $this columnNum(int $value) 列数
 * @method $this options(array $value) 列表项配置项
 */
class GridNav extends BaseSchema
{

    public string $type = 'grid-nav';
}
