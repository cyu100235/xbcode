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
namespace plugin\xbCode\builder\Components\Form;

/**
 * 评分组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-rating
 * @method $this value(string $value) 当前值
 * @method $this count(int $value) 总星数
 * @method $this half(bool $value) 是否使用半星选择
 * @method $this allowClear(bool $value) 是否允许再次点击后清除
 * @method $this readonly(bool $value) 是否只读
 * @method $this colors(array $value) 选中的颜色。传入字符串，则只有一种颜色。对象可自定义分段，键名为分段的界限值，键值为对应的类名
 * @method $this inactiveColor(string $value) 未被选中的星星的颜色
 * @method $this texts(array $value) 星星被选中时的提示文字。可自定义分段，键名为分段的界限值，键值为对应的类名
 * @method $this textPosition(string $value) 文字的位置
 * @method $this char(string $value) 自定义字符
 * @method $this charClassName(string $value) 自定义字符类名
 * @method $this textClassName(string $value) 自定义文字类名
 */
class InputRating extends FormBase
{
    public string $type = "input-rating";
}
