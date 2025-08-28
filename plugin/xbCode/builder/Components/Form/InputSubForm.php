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
 * 输入子表单组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-sub-form
 * @method $this multiple(string $value) 设置是否为多选模式
 * @method $this labelField(string $value) 设置当值中存在这个字段，则按钮名称将使用此字段的值来展示。
 * @method $this btnLabel(string $value) 设置按钮默认名称
 * @method $this minLength(int $value) 设置限制最小个数。
 * @method $this maxLength(int $value) 设置限制最大个数。
 * @method $this draggable(bool $value) 设置是否可拖拽排序
 * @method $this addable(bool $value) 设置是否可新增
 * @method $this removable(bool $value) 设置是否可删除
 * @method $this addButtonClassName(string $value) 设置新增按钮 CSS 类名
 * @method $this itemClassName(string $value) 设置值元素 CSS 类名
 * @method $this itemsClassName(string $value) 设置值包裹元素 CSS 类名
 * @method $this form(Form $value) 设置子表单配置，同 Form
 * @method $this addButtonText(string $value) 设置自定义新增一项的文本
 * @method $this showErrorMsg(bool $value) 设置是否在左下角显示报错信息
 */
class InputSubForm extends FormBase
{
    public string $type = 'input-sub-form';
}
