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
 * 季度范围
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-quarter-range
 * @method $this valueFormat(string $value) 设置日期选择器值格式
 * @method $this displayFormat(string $value) 设置日期选择器显示格式
 * @method $this placeholder(string $value) 设置占位文本
 * @method $this minDate(string $value) 设置限制最小日期
 * @method $this maxDate(string $value) 设置限制最大日期
 * @method $this minDuration(string $value) 设置限制最小跨度，如： 2quarter
 * @method $this maxDuration(string $value) 设置限制最大跨度，如：4quarter
 * @method $this utc(bool $value) 设置保存 UTC 值
 * @method $this clearable(bool $value) 设置是否可清除
 * @method $this embed(bool $value) 设置是否内联模式
 * @method $this animation(bool $value) 设置是否启用游标动画
 * @method $this extraName(string $value) 设置是否存成两个字段
 * @method $this popOverContainerSelector(string $value) 设置弹层挂载位置选择器，会通过querySelector获取
 */
class InputQuarterRange extends InputDateRange
{
    public string $type = "input-quarter-range";
}
