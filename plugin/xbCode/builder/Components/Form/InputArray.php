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
 * 数组输入框
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-array
 * @method $this items(mixed $value) 设置单项表单类型
 * @method $this addable(bool $value) 是否可新增
 * @method $this removable(bool $value) 是否可删除
 * @method $this draggable(bool $value) 是否可以拖动排序，默认 false
 * @method $this draggableTip(string $value) 可拖拽的提示文字，默认为："可通过拖动每行中的【交换】按钮进行顺序调整"
 * @method $this addButtonText(string $value) 新增按钮文字，默认为 "新增"
 * @method $this minLength(int $value) 限制最小长度
 * @method $this maxLength(int $value) 限制最大长度
 * @method $this scaffold(mixed $value) 新增成员时的默认值，一般根据 items 的数据类型指定需要的默认值
 */
class InputArray extends FormBase
{
    public string $type = 'input-array';
}
