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
 * 折叠面板组组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this activeKey(string $value) 当前展开的面板
 * @method $this accordion(bool $value) 是否手风琴模式
 * @method $this expandIcon(string $value) 展开图标
 * @method $this expandIconPosition(string $value) 展开图标位置
 * @method $this body(string|array $value) 内容
 * @method $this className(string $value) 设置外层 Dom 的类名
 * @method $this style(string $value) 设置外层 Dom 的样式
 */
class CollapseGroup extends BaseSchema
{
    public string $type = 'collapse-group';
}
