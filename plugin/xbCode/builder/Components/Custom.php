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
 * 自定义组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/custom
 * @method $this id(string $value) 节点 id
 * @method $this name(string $value) 节点名称
 * @method $this className(string $value) 节点 class
 * @method $this inline(string $value) 默认使用 div 标签，如果 true 就使用 span 标签
 * @method $this html(string $value) 初始化节点 html
 * @method $this onMount(string $value) 节点初始化之后调的用函数
 * @method $this onUpdate(string $value) 数据有更新的时候调用的函数
 * @method $this onUnmount(string $value) 节点销毁的时候调用的函数
 */
class Custom extends BaseSchema
{
    public string $type = 'custom';
}
