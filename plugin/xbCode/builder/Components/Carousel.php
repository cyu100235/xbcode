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
 * 轮播图组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/carousel
 * @method $this className(string $value) 设置组件的类名
 * @method $this options(array $value) 设置轮播面板数据
 * @method $this itemSchema(array $value) 设置自定义schema来展示数据
 * @method $this auto(bool $value) 设置是否自动轮播
 * @method $this interval(string $value) 设置切换动画间隔
 * @method $this duration(int $value) 设置切换动画时长（ms）
 * @method $this width(string $value) 设置宽度
 * @method $this height(string $value) 设置高度
 * @method $this controls(array $value) 设置显示左右箭头、底部圆点索引
 * @method $this controlsTheme(string $value) 设置左右箭头、底部圆点索引颜色
 * @method $this animation(string $value) 设置切换动画效果
 * @method $this thumbMode(string $value) 设置图片默认缩放模式
 * @method $this multiple(array $value) 设置多图展示
 * @method $this alwaysShowArrow(bool $value) 设置是否一直显示箭头
 * @method $this icons(array $value) 设置自定义箭头图标
 */
class Carousel extends BaseSchema
{
    public string $type = 'carousel';
}
