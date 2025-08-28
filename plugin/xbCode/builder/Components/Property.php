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
 * 属性表组件
 * 表格的方式显示只读信息，如产品详情页
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/property
 * @method $this className(string $value) 设置外层 CSS 类名
 * @method $this style(array $value) 设置外层 DOM 的样式
 * @method $this labelStyle(array $value) 设置属性名的样式
 * @method $this contentStyle(array $value) 设置属性值的样式
 * @method $this column(int $value) 每行几列
 * @method $this mode(string $value) 显示模式，目前只有 'table' 和 'simple'
 * @method $this separator(string $value) 'simple' 模式下属性名和值之间的分隔符
 * @method $this title(string $value) 标题
 * @method $this source(string $value) 数据源
 * @method $this items(array $value) 属性列表
 */
class Property extends BaseSchema
{
    public string $type = 'property';
}
