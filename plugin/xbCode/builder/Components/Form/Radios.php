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
 * 单选框
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/radios
 * @method $this options(string $value) 选项组
 * @method $this source(string $source) 动态选项组
 * @method $this labelField(string $labelField) 选项标签字段
 * @method $this valueField(string $valueField) 选项值字段
 * @method $this columnsCount(int $columnsCount) 选项按几列显示，默认为一列
 * @method $this inline(bool $inline) 是否显示为一行
 * @method $this selectFirst(bool $selectFirst) 是否默认选中第一个
 * @method $this autoFill(array $autoFill) 自动填充
 */
class Radios extends FormBase
{
    public string $type = 'radios';
}
