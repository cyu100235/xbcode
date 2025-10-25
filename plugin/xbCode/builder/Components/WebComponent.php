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
 * WebComponent 组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/web-component
 * @method $this tag(string $value) 组件标签
 * @method $this body(mixed $value) 组件内容
 * @method $this props(array $value) 组件属性
 */
class WebComponent extends BaseSchema
{
    public string $type = 'web-component';
}
