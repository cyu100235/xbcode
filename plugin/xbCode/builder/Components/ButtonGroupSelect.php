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
namespace plugin\xbCode\builder\Components;

/**
 * 单选按钮组
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/alert
 * @method $this vertical(bool $value) 是否使用垂直模式
 * @method $this tiled(bool $value) 是否使用平铺模式
 * @method $this btnLevel(string $value) 按钮样式'link' | 'primary' | 'secondary' | 'info'|'success' | 'warning' | 'danger' | 'light'| 'dark' | 'default'
 * @method $this btnActiveLevel(string $value) 选中按钮样式'link' | 'primary' | 'secondary' | 'info'|'success' | 'warning' | 'danger' | 'light'| 'dark' | 'default'
 * @method $this options(array $value) 选项组
 * @method $this multiple(bool $value) 是否多选
 * @method $this labelField(string $value) 名称字段
 * @method $this valueField(string $value) 标签字段
 * @method $this joinValues(bool $value) 拼接值
 * @method $this extractValue(bool $value) 提取值
 * @method $this autoFill(array $value) 自动填充
 */
class ButtonGroupSelect extends BaseSchema
{
    public string $type = 'button-group-select';
}
