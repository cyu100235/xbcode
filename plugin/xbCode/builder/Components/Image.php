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
 * 图片组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/image
 * @method $this className(string $value) 设置外层 CSS 类名
 * @method $this imageClassName(string $value) 设置图片 CSS 类名
 * @method $this thumbClassName(string $value) 设置图片缩率图 CSS 类名
 * @method $this height(string $value) 设置图片缩率高度
 * @method $this width(string $value) 设置图片缩率宽度
 * @method $this title(string $value) 设置标题
 * @method $this imageCaption(string $value) 设置描述
 * @method $this placeholder(string $value) 设置占位文本
 * @method $this defaultImage(string $value) 设置无数据时显示的图片
 * @method $this src(string $value) 设置缩略图地址
 * @method $this href(string $value) 设置外部链接地址
 * @method $this originalSrc(string $value) 设置原图地址
 * @method $this enlargeAble(string $value) 设置支持放大预览
 * @method $this enlargeTitle(string $value) 设置放大预览的标题
 * @method $this enlargeCaption(string $value) 设置放大预览的描述
 * @method $this enlargeWithGallary(string $value) 在表格中，图片的放大功能会默认展示所有图片信息，为false将关闭放大图片集列表展示
 * @method $this thumbMode(string $value) 设置预览图模式，可选：'w-full', 'h-full', 'contain', 'cover'
 * @method $this thumbRatio(string $value) 设置预览图比例，可选：'1:1', '4:3', '16:9'
 * @method $this imageMode(string $value) 设置图片展示模式，可选：'thumb', 'original' 即：缩略图模式 或者 原图模式
 * @method $this showToolbar(string $value) 设置放大模式下是否展示图片的工具栏
 * @method $this toolbarActions(ImageAction[] $value) 设置图片工具栏，支持旋转，缩放，默认操作全部开启
 * @method $this maxScale(string $value) 设置执行调整图片比例动作时的最大百分比
 * @method $this minScale(string $value) 设置执行调整图片比例动作时的最小百分比
 */
class Image extends BaseSchema
{
    public string $type = 'image';
}
