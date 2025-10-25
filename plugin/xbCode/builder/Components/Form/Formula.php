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
 * 基本用法
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/formula
 * @method $this name(string $value) 设置需要应用的表单项name值，公式结果将作用到此处指定的变量中去。
 * @method $this formula(string $value) 设置应用的公式。
 * @method $this condition(string $value) 设置公式作用条件。
 * @method $this initSet(bool $value) 设置初始化时是否设置，默认为 true。
 * @method $this autoSet(bool $value) 设置观察公式结果，如果计算结果有变化，则自动应用到变量上，默认为 true。
 * @method $this id(string $value) 定义名字，当按钮的目标指定为此值后，这个机制可以在 autoSet 为 false 时用来手动触发
 */
class Formula extends FormBase
{
    public string $type = 'formula';
}
