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
 * 包裹容器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/wrapper
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this size(string $value) 支持: xs、sm、md和lg
 * @method $this style(string $value) 自定义样式
 * @method $this body(SchemaNode $value) 内容容器
 */
class Wrapper extends BaseSchema
{
    public string $type = 'wrapper';
}
