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
 * 级联选择器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/nestedselect
 * @method $this options(array $options) 选项组
 * @method $this source(string $source) 动态选项组
 * @method $this delimiter(string $delimiter) 拼接符
 * @method $this labelField(string $labelField) 选项标签字段
 * @method $this valueField(string $valueField) 选项值字段
 * @method $this joinValues(bool $joinValues) 拼接值
 * @method $this extractValue(bool $extractValue) 提取值
 * @method $this autoFill(array $autoFill) 自动填充
 * @method $this cascade(bool $cascade) 设置 true时，当选中父节点时不自动选择子节点。
 * @method $this withChildren(bool $withChildren) 设置 true时，选中父节点时，值里面将包含子节点的值，否则只会保留父节点的值。
 * @method $this onlyChildren(bool $onlyChildren) 多选时，选中父节点时，是否只将其子节点加入到值中。
 * @method $this searchable(bool $searchable) 可否搜索
 * @method $this searchPromptText(string $searchPromptText) 搜索框占位文本
 * @method $this noResultsText(string $noResultsText) 无结果时的文本
 * @method $this multiple(bool $multiple) 可否多选
 * @method $this hideNodePathLabel(bool $hideNodePathLabel) 是否隐藏选择框中已选择节点的路径 label 信息
 * @method $this onlyLeaf(bool $onlyLeaf) 只允许选择叶子节点
 * @method $this maxTagCount(int $maxTagCount) 标签的最大展示数量，超出数量后以收纳浮层的方式展示，仅在多选模式开启后生效
 * @method $this overflowTagPopover(array $overflowTagPopover) 收纳浮层的配置属性，详细配置参考Tooltip
 */
class NestedSelect extends FormOptions
{
    public string $type = 'nested-select';
}
