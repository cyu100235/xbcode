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
 * 卡片组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link hhttps://aisuda.bce.baidu.com/amis/zh-CN/components/card
 * @method $this className(string $value) 设置组件的类名
 * @method $this href(string $value) 设置外部链接
 * @method $this header(array $value) 设置卡片头部内容设置
 * @method $this body(array $value) 设置内容容器，主要用来放置非表单项组件
 * @method $this actions(array $value) 设置按钮集合
 * @method $this actionsCount(int $value) 设置按钮集合每行个数
 * @method $this itemAction(array $value) 设置点击卡片的行为
 * @method $this media(array $value) 设置多媒体部内容设置
 * @method $this secondary(string $value) 设置次要说明
 * @method $this toolbar(array $value) 设置工具栏按钮
 * @method $this dragging(bool $value) 设置是否显示拖拽图标
 * @method $this selectable(bool $value) 设置卡片是否可选
 * @method $this checkable(bool $value) 设置卡片选择按钮是否禁用
 * @method $this selected(bool $value) 设置卡片选择按钮是否选中
 * @method $this hideCheckToggler(bool $value) 设置卡片选择按钮是否隐藏
 * @method $this multiple(bool $value) 设置卡片是否为多选
 * @method $this useCardLabel(bool $value) 设置卡片内容区的表单项 label 是否使用 Card 内部的样式
 */
class Card extends BaseSchema
{
    public string $type = 'card';
}
