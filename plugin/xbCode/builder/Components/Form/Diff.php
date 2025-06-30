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
 * 比较编辑器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/diff-editor
 * @method $this diffValue(string $value) 	左侧值，可以使用数据链变量
 * @method $this value(string $value) 	右侧值
 * @method $this language(string $value) 	语言类型
 * @method $this options(string $value) 	配置项
 * @method $this disabled(bool $value) 	是否禁用
 * @method $this disabledOn(string $value) 	禁用条件表达式
 */
class Diff extends FormBase
{
    public string $type = 'diff-editor';
}
