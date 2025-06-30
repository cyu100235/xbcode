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
 * 条件构建器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/condition-builder
 * @method $this className(string $value) 外层 DOM 类名
 * @method $this fieldClassName(string $value) 输入字段的类名
 * @method $this source(string $value) 通过远程拉取配置项
 * @method $this embed(bool $value) 内嵌展示
 * @method $this title(string $value) 弹窗配置的顶部标题
 * @method $this fields(array $value) 字段配置
 * @method $this showANDOR(bool $value) 用于 simple 模式下显示切换按钮
 * @method $this showNot(bool $value) 是否显示「非」按钮
 * @method $this draggable(bool $value) 是否可拖拽
 * @method $this searchable(bool $value) 字段是否可搜索
 * @method $this selectMode(string $value) 组合条件左侧选项类型。'chained'模式需要3.2.0及以上版本
 * @method $this addBtnVisibleOn(string $value) 表达式：控制按钮“添加条件”的显示。参数为depth、breadth，分别代表深度、长度
 * @method $this addGroupBtnVisibleOn(string $value) 表达式：控制按钮“添加条件组”的显示。参数为depth、breadth，分别代表深度、长度
 * @method $this inputSettings(array $value) 开启公式编辑模式时的输入控件类型
 * @method $this formula(array $value) 字段输入控件变成公式编辑器
 * @method $this showIf(bool $value) 开启后条件中额外还能配置启动条件
 * @method $this formulaForIf(array $value) 给 showIF 表达式用的公式信息
 */
class ConditionBuilder extends FormBase
{
    public string $type = 'condition-builder';
}
