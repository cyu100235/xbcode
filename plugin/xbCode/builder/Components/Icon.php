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
 * 图标组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/icon
 * @method $this className(string $value) 设置外层 CSS 类名
 * @method $this icon(string $value) 图标名称
 * @method $this size(int|string $value) 图标大小
 * @method $this color(string $value) 图标颜色
 */
class Icon extends BaseSchema
{
    public string $type = 'xbIcons';
}
