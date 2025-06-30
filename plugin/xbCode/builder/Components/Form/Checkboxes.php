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
 * 复选框组组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/checkboxes
 * @method $this options(string $value) 选项组
 * @method $this source(string $value) 动态选项组
 * @method $this delimiter(string $value) 拼接符
 * @method $this labelField(string $value) 选项标签字段
 * @method $this valueField(string $value) 选项值字段
 * @method $this joinValues(bool $value) 拼接值
 * @method $this extractValue(bool $value) 提取值
 * @method $this columnsCount(int $value) 选项按几列显示，默认为一列
 * @method $this menuTpl(string $value) 支持自定义选项渲染
 * @method $this checkAll(bool $value) 是否支持全选
 * @method $this inline(bool $value) 是否显示为一行
 * @method $this defaultCheckAll(bool $value) 默认是否全选
 * @method $this creatable(bool $value) 新增选项
 * @method $this createBtnLabel(string $value) 新增选项按钮文本
 * @method $this addControls(array $value) 自定义新增表单项
 * @method $this addApi(string $value) 配置新增选项接口
 * @method $this editable(bool $value) 编辑选项
 * @method $this editControls(array $value) 自定义编辑表单项
 * @method $this editApi(string $value) 配置编辑选项接口
 * @method $this removable(bool $value) 删除选项
 * @method $this deleteApi(string $value) 配置删除选项接口
 * @method $this optionType(string $value) 选项类型，default | button
 * @method $this itemClassName(string $value) 选项样式类名
 * @method $this labelClassName(string $value) 选项标签样式类名
 */
class Checkboxes extends FormBase
{
    public string $type = 'checkboxes';
}
