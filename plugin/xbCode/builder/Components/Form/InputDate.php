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
 * 日期选择器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-date
 * @method $this value(string $value) 设置默认值
 * @method $this valueFormat(string $value) 设置日期选择器值格式，更多格式类型请参考 文档
 * @method $this displayFormat(string $value) 设置日期选择器显示格式，即时间戳格式，更多格式类型请参考 文档
 * @method $this closeOnSelect(bool $value) 点选日期后，是否马上关闭选择框
 * @method $this placeholder(string $value) 设置占位文本
 * @method $this shortcuts(string|array $value) 设置日期快捷键，字符串格式为预设值，对象格式支持写表达式
 * @method $this minDate(string $value) 限制最小日期
 * @method $this maxDate(string $value) 限制最大日期
 * @method $this utc(bool $value) 保存 utc 值
 * @method $this clearable(bool $value) 是否可清除
 * @method $this embed(bool $value) 是否内联模式
 * @method $this disabledDate(string $value) 用字符函数来控制哪些天不可以被点选
 * @method $this popOverContainerSelector(string $value) 弹层挂载位置选择器，会通过querySelector获取
 */
class InputDate extends FormBase
{
    public string $type = 'input-date';

    public function __construct()
    {
        $this->format("YYYY-MM-DD HH:mm:ss");
    }

    public function datetime(): InputDate
    {
        $this->type = 'input-datetime';
        return $this;
    }
}
