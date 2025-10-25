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
 * 颜色选择器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-color
 * @method $this format(string $value) 设置颜色格式，支持 hex、hexa、hls、rgb 或 rgba
 * @method $this presetColors(array $value) 设置预设颜色值，选择器底部的默认颜色
 * @method $this allowCustomColor(bool $value) 是否允许自定义颜色，默认为 true
 * @method $this clearable(bool $value) 是否显示清除按钮，默认为 true
 * @method $this resetValue(string $value) 清除后，表单项值调整成该值，默认为空字符串
 */
class InputColor extends FormBase
{
    public string $type = 'input-color';
}
