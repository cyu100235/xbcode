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
 * 列表选择
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/list-select
 * @method $this options(array $value) 选项组
 * @method $this source(string $value) 动态选项组
 * @method $this multiple(bool $value) 多选
 * @method $this labelField(string $value) 选项标签字段
 * @method $this valueField(string $value) 选项值字段
 * @method $this joinValues(bool $value) 拼接值
 * @method $this extractValue(bool $value) 提取值
 * @method $this autoFill(array $value) 自动填充
 * @method $this listClassName(string $value) 支持配置 list div 的 css 类名。比如: flex justify-between
 */
class ListSelect extends FormOptions
{
    public string $type = 'list-select';
}
