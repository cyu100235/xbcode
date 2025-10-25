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
 * 表格视图组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/table-view
 * @method $this width(string|int $value) 表格宽度
 * @method $this padding(string|int $value) 表格内边距
 * @method $this border(string|int $value) 表格边框
 * @method $this borderColor(string $value) 表格边框颜色
 * @method $this caption(string $value) 表格标题
 * @method $this trs(string $value) 表格行数
 * @method $this cols(string $value) 表格列数
 * @method $this className(string $value) 表格外层类名
 */
class TableView extends BaseSchema
{
    public string $type = 'table-view';
}
