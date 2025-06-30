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
 * 头像渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/avatar
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this style(array $value) 外层 Dom 的样式
 * @method $this fit(string $value) 具体细节可以参考 MDN 文档
 * @method $this src(string $value) 图片地址
 * @method $this defaultAvatar(string $value) 占位图
 * @method $this text(string $value) 文字
 * @method $this icon(string $value) 图标
 * @method $this shape(string $value) 形状，有三种 'circle' （圆形）、'square'（正方形）、'rounded'（圆角）
 * @method $this size(string $value) 'default' | 'normal' | 'small'三种类型代表不同大小（分别是 48、40、32），也可以直接数字
 * @method $this gap(string $value) 控制字符类型距离左右两侧边界单位像素
 * @method $this alt(string $value) 图像无法显示时的替代文本
 * @method $this draggable(string $value) 图片是否允许拖动
 * @method $this crossOrigin(string $value) 图片的 CORS 属性设置
 * @method $this onError(string $value) 图片加载失败的字符串，这个字符串是一个 New Function 内部执行的字符串，参数是 event（使用 event.nativeEvent 获取原生 dom 事件），这个字符串需要返回 boolean 值。设置 "return ture;" 会在图片加载失败后，使用 text 或者 icon 代表的信息来进行替换。目前图片加载失败默认是不进行置换。注意：图片加载失败，不包括$获取数据为空情况
 */
class Avatar extends BaseSchema
{
    public string $type = 'avatar';

    public function defaultAttr()
    {
        if (!$this->src) {
            $name = $this->name;
            $this->src('${' . $name . '}');
        }

    }

    public function getValue($value)
    {
        // return admin_file_url($value);
        return '';
    }
}
