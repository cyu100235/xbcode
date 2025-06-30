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
 * 输入标签组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-tag
 * @method $this options(array $value) 选项组
 * @method $this optionsTip(array $value) "最近您使用的标签" 选项提示
 * @method $this source(string|ApiSchema $value) 动态选项组
 * @method $this delimiter(string $value) 拼接符
 * @method $this labelField(string $value) 选项标签字段
 * @method $this valueField(string $value) 选项值字段
 * @method $this joinValues(bool $value) 拼接值
 * @method $this extractValue(bool $value) 提取值
 * @method $this clearable(bool $value) 在有值的时候是否显示一个删除图标在右侧
 * @method $this resetValue(string $value) 删除后设置此配置项给定的值
 * @method $this max(int $value) 允许添加的标签的最大数量
 * @method $this maxTagLength(int $value) 单个标签的最大文本长度
 * @method $this maxTagCount(int $value) 标签的最大展示数量，超出数量后以收纳浮层的方式展示，仅在多选模式开启后生效
 * @method $this overflowTagPopover(TooltipObject $value) 收纳浮层的配置属性，详细配置参考Tooltip
 * @method $this enableBatchAdd(bool $value) 是否开启批量添加模式
 * @method $this separator(string $value) 开启批量添加后，输入多个标签的分隔符，支持传入多个符号，默认为"-"
 * @method $this placeholder(string $value) 输入框占位符
 */
class InputTag extends FormOptions
{
    public string $type = 'input-tag';
}
