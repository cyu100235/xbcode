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
 * 映射组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/mapping
 * @method $this className(string $value) 设置外层 CSS 类名
 * @method $this placeholder(string $value) 设置占位文本
 * @method $this map(array $value) 设置映射配置
 * @method $this source(string $value) 设置API 或 数据映射
 * @method $this valueField(string $value) 设置映射的字段名
 * @method $this labelField(string $value) 设置展示的字段名
 * @method $this itemSchema(string $value) 设置自定义渲染模板，支持html或schemaNode
 */
class Mapping extends BaseSchema
{
    public string $type = 'map';
}
