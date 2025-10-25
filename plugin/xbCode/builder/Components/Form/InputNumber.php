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
 * 数字输入框
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-number
 * @method $this min(string $value) 设置最小值
 * @method $this max(string $value) 设置最大值
 * @method $this step(int $value) 设置步长
 * @method $this precision(int $value) 设置精度，即小数点后几位，支持 0 和正整数
 * @method $this showSteps(bool $value) 是否显示上下点击按钮
 * @method $this readOnly(bool $value) 设置为只读
 * @method $this prefix(string $value) 设置前缀
 * @method $this suffix(string $value) 设置后缀
 * @method $this unitOptions(array $value) 设置单位选项
 * @method $this kilobitSeparator(bool $value) 千分分隔
 * @method $this keyboard(bool $value) 键盘事件（方向上下）
 * @method $this big(bool $value) 是否使用大数
 * @method $this displayMode(string $value) 设置样式类型
 * @method $this borderMode(string $value) 设置边框模式，全边框，还是半边框，或者没边框
 * @method $this resetValue(mixed $value) 清空输入内容时，组件值将设置为 resetValue
 * @method $this clearValueOnEmpty(bool $value) 内容为空时从数据域中删除该表单项对应的值
 */
class InputNumber extends FormBase
{
    public string $type = 'input-number';
}
