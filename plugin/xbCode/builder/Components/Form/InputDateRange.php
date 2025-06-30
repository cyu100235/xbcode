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
 * 日期范围选择器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-date-range
 * @method $this valueFormat(string $value) 日期选择器值格式
 * @method $this displayFormat(string $value) 日期选择器显示格式
 * @method $this placeholder(string $value) 占位文本
 * @method $this shortcuts(string|array $value) 日期范围快捷键
 * @method $this minDate(string $value) 限制最小日期
 * @method $this maxDate(string $value) 限制最大日期
 * @method $this minDuration(string $value) 限制最小跨度，如： 2days
 * @method $this maxDuration(string $value) 限制最大跨度，如：1year	
 * @method $this utc(bool $value) 保存 UTC 值
 * @method $this clearable(bool $value) 是否可清除
 * @method $this embed(bool $value) 是否内联模式
 * @method $this animation(bool $value) 是否启用游标动画
 * @method $this extraName(string $value) 是否存成两个字段
 * @method $this transform(string $value) 日期数据处理函数，用来处理选择日期之后的的值，返回值为 Moment 对象
 * @method $this popOverContainerSelector(string $value) 弹层挂载位置选择器，会通过 querySelector 获取
 */
class InputDateRange extends FormBase
{
    public string $type = 'input-date-range';

    public function __construct()
    {
        $this->format("YYYY-MM-DD HH:mm:ss");
        $this->ranges(['today', 'yesterday', 'prevweek', 'thisweek', 'thismonth', 'prevmonth', '7daysago']);
    }

    public function datetime(): InputDateRange
    {
        $this->type = 'input-datetime-range';
        return $this;
    }
}
