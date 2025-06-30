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
 * 模板渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/tpl
 * @method $this className(string $value) 外层类名
 * @method $this tpl(string $value) 模板内容
 * @method $this html(string $value) html内容
 * @method $this text(string $value) 文本内容
 * @method $this raw(string $value) 原始内容
 * @method $this inline(string $value) 内联内容
 * @method $this style(string $value) 内联样式
 * @method $this badge(string $value) 徽章内容
 * @method $this showNativeTitle(bool $value) 是否设置外层 DOM 节点的 title 属性为文本内容
 */
class Tpl extends BaseSchema
{
    public string $type = 'tpl';
}
