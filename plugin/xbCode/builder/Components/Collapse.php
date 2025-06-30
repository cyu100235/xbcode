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
 * 折叠面板组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/collapse
 * @method $this disabled(bool $value) 是否禁用
 * @method $this collapsable(bool $value) 是否可折叠
 * @method $this collapsed(bool $value) 是否折叠
 * @method $this showArrow(bool $value) 是否显示箭头
 * @method $this key(string|int $value) 唯一标识
 * @method $this header(string|array $value) 标题
 * @method $this body(string|array $value) 内容
 */
class Collapse extends BaseSchema
{
    public string $type = 'collapse';
}
