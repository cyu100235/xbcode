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
namespace plugin\xbCode\builder\Components\Table;

use plugin\xbCode\builder\Components\BaseSchema;

/**
 * 表格列组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/table
 * @method $this type(string $value) 设置列类型，默认：text文本
 * @method $this label(string $value) 设置列标题
 * @method $this name(string $value) 设置列名称
 * @method $this fixed(string $value) 设置列是否固定
 * @method $this popOver(string $value) 设置列的弹出框内容
 * @method $this copyable(mixed $value) 设置列是否可复制
 * @method $this style(array $value) 设置单元格自定义样式
 * @method $this innerStyle(array $value) 设置单元格内部组件自定义样式
 * @method $this align(string $value) 设置单元格对齐方式
 * @method $this headerAlign(string $value) 设置表头单元格对齐方式
 * @method $this vAlign(string $value) 设置单元格垂直对齐方式
 * @method $this textOverflow(string $value) 设置文本溢出后展示形式
 * @method $this width(int|string $value) 设置列宽
 * @method $this remark(string $value) 设置提示信息
 */
class TableColumn extends BaseSchema
{
    public string $type = 'text';
}
