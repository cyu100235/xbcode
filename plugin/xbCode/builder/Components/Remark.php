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
 * 标记备注组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/remark
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this content(string $value) 提示文本
 * @method $this placement(string $value) 弹出位置
 * @method $this trigger(string $value) 触发条件
 * @method $this icon(string $value) 图标
 * @method $this shape(string $value) 图标形状
 */
class Remark extends BaseSchema
{
    public string $type = 'remark';
}
