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
 * 列表组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/list
 * @method $this title(string $value) 标题
 * @method $this source(string $value) ${items} 数据源, 获取当前数据域变量，支持数据映射
 * @method $this placeholder(string $value) 当没数据的时候的文字提示
 * @method $this selectable(bool $value) 列表是否可选
 * @method $this multiple(bool $value) 列表是否为多选
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this headerClassName(string $value) 顶部外层 CSS 类名
 * @method $this footerClassName(string $value) 底部外层 CSS 类名
 * @method $this listItem(array $value) 配置单条信息
 */
class ListSchema extends BaseSchema
{
    public string $type = 'list';
}
