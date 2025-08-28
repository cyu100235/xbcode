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
 * 滑块组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-range
 * @method $this className(string $value) 设置css类名
 * @method $this value(mixed $value) 设置值
 * @method $this min(mixed $value) 设置最小值
 * @method $this max(mixed $value) 设置最大值
 * @method $this disabled(bool $value) 设置是否禁用
 * @method $this step(mixed $value) 设置步长
 * @method $this showSteps(bool $value) 设置是否显示步长
 * @method $this parts(mixed $value) 设置分割的块数
 * @method $this marks(mixed $value) 设置刻度标记
 * @method $this tooltipVisible(bool $value) 设置是否显示滑块标签
 * @method $this tooltipPlacement(string $value) 设置滑块标签的位置
 * @method $this tipFormatter(callable $value) 设置控制滑块标签显隐函数
 * @method $this multiple(bool $value) 设置是否支持选择范围
 * @method $this joinValues(bool $value) 设置是否将选择的值通过分隔符连接起来
 * @method $this delimiter(string $value) 设置分隔符
 * @method $this unit(string $value) 设置单位
 * @method $this clearable(bool $value) 设置是否可清除
 * @method $this showInput(bool $value) 设置是否显示输入框
 * @method $this showInputUnit(bool $value) 设置是否显示输入框单位
 */
class InputRange extends FormBase
{
    public string $type = 'input-range';
}
