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
 * 公式编辑器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-formula
 * @method $this title(string $value) 弹框标题
 * @method $this header(string $value) 编辑器 header 标题，如果不设置，默认使用表单项label字段
 * @method $this evalMode(bool $value) 表达式模式 或者 模板模式，模板模式则需要将表达式写在 ${ 和 } 中间。
 * @method $this variables(array $value) 可用变量
 * @method $this variableMode(string $value) 可配置成 tabs 或者 tree 默认为列表，支持分组。
 * @method $this functions(array $value) 可以不设置，默认就是 amis-formula 里面定义的函数，如果扩充了新的函数则需要指定
 * @method $this inputMode(string $value) 控件的展示模式
 * @method $this icon(string $value) 按钮图标，例如fa fa-list
 * @method $this btnLabel(string $value) 按钮文本，inputMode为button时生效
 * @method $this level(string $value) 按钮样式
 * @method $this allowInput(bool $value) 输入框是否可输入
 * @method $this btnSize(string $value) 按钮大小
 * @method $this borderMode(string $value) 输入框边框模式
 * @method $this placeholder(string $value) 输入框占位符
 * @method $this variableClassName(string $value) 变量面板 CSS 样式类名
 * @method $this functionClassName(string $value) 函数面板 CSS 样式类名
 */
class InputFormula extends FormBase
{
    public string $type = 'input-formula';
}
