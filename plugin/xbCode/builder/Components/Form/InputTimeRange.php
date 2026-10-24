<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components\Form;

/**
 * 时间范围选择器组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-time-range
 * @method $this value(string $value) 默认值
 * @method $this valueFormat(string $value) 时间选择器值格式
 * @method $this displayFormat(string $value) 时间选择器显示格式
 * @method $this placeholder(string $value) 占位文本
 * @method $this clearable(bool $value) 是否可清除
 * @method $this embed(bool $value) 是否内联模式
 * @method $this animation(bool $value) 是否启用游标动画
 * @method $this extraName(string $value) 结束字段名
 * @method $this popOverContainerSelector(string $value) 弹层挂载位置选择器
 */
class InputTimeRange extends FormOptions
{
    public string $type = 'input-time-range';
}
