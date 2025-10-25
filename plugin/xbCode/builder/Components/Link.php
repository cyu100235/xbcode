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

// body	string		标签内文本
// href	string		链接地址
// blank	boolean		是否在新标签页打开
// htmlTarget	string		a 标签的 target，优先于 blank 属性
// title	string		a 标签的 title
// disabled	boolean		禁用超链接
// icon	string		超链接图标，以加强显示
// rightIcon	string		右侧图标
/**
 * 链接组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/link
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this blank(bool $value) 是否在新标签页打开
 * @method $this href(string $value) 链接地址
 * @method $this body(string $value) 标签内文本
 * @method $this htmlTarget(string $value) a 标签的 target，优先于 blank 属性
 * @method $this title(string $value) a 标签的 title
 * @method $this disabled(bool $value) 禁用超链接
 * @method $this icon(string $value) 超链接图标，以加强显示
 * @method $this rightIcon(string $value) 右侧图标
 */
class Link extends BaseSchema
{
    public string $type = 'link';
}
