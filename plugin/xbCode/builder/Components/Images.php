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
 * 图片集组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/images
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this defaultImage(string $value) 默认展示图片
 * @method $this value(string $value) 图片数组
 * @method $this source(string $value) 数据源
 * @method $this delimiter(string $value) 分隔符，当 value 为字符串时，用该值进行分隔拆分
 * @method $this src(string $value) 预览图地址，支持数据映射获取对象中图片变量
 * @method $this originalSrc(string $value) 原图地址，支持数据映射获取对象中图片变量
 * @method $this enlargeAble(string $value) 支持放大预览
 * @method $this enlargeWithGallary(string $value) 默认在放大功能展示图片集的所有图片信息；表格中使用时，设置为true将展示所有行的图片信息；设置为false将关闭放大模式下图片集列表的展示
 * @method $this thumbMode(string $value) 预览图模式，可选：'w-full', 'h-full', 'contain', 'cover'
 * @method $this thumbRatio(string $value) 预览图比例，可选：'1:1', '4:3', '16:9'
 * @method $this showToolbar(string $value) 放大模式下是否展示图片的工具栏
 * @method $this toolbarActions(string $value) 图片工具栏，支持旋转，缩放，默认操作全部开启
 */
class Images extends BaseSchema
{
    public string $type = 'images';

    /**
     * 静态模式
     * @return $this
     */
    public function static()
    {
        $this->type = 'static-images';
        return $this;
    }
}
