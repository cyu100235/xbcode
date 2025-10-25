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
 * 容器组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/container
 * @method $this className(string $value) 外层 Dom 的类名
 * @method $this bodyClassName(string $value) 容器内容区的类名
 * @method $this wrapperComponent(string $value) 容器标签名
 * @method $this style(string $value) 自定义样式
 * @method $this body(SchemaNode $value) 容器内容
 */
class Container extends BaseSchema
{
    public string $type = 'container';
}
