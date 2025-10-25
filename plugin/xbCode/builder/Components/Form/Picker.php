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
 * 列表选择器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/picker
 * @method $this options(options $value) 选项组
 * @method $this source(string $value) 动态选项组
 * @method $this multiple(bool $value) 是否为多选
 * @method $this delimiter(string $value) 拼接符
 * @method $this labelField(string $value) 选项标签字段
 * @method $this valueField(string $value) 选项值字段
 * @method $this joinValues(bool $value) 拼接值
 * @method $this extractValue(bool $value) 提取值
 * @method $this autoFill(array $value) 自动填充
 * @method $this modalTitle(string $value) 设置模态框的标题
 * @method $this modalMode(string $value) 设置 dialog 或者 drawer，用来配置弹出方式
 * @method $this pickerSchema(string $value) 即用 List 类型的渲染，来展示列表信息。更多配置参考 CRUD
 * @method $this embed(bool $value) 是否使用内嵌模式
 * @method $this overflowConfig(array $value) 开启最大标签展示数量的相关配置
 * @method $this itemClearable(bool $value) 用于控制是否显示选中项的删除图标，默认值为 true
 */
class Picker extends FormOptions
{
    public string $type = 'picker';
}
