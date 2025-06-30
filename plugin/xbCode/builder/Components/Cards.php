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
 * 多卡片组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/cards?page=1
 * @method $this title(string $value) 设置标题
 * @method $this source(string $value) 设置数据源
 * @method $this placeholder(string $value) 设置当没数据的时候的文字提示
 * @method $this className(string $value) 设置外层 CSS 类名
 * @method $this headerClassName(string $value) 设置顶部外层 CSS 类名
 * @method $this footerClassName(string $value) 设置底部外层 CSS 类名
 * @method $this itemClassName(string $value) 设置卡片 CSS 类名
 * @method $this card(string $value) 设置卡片信息
 * @method $this selectable(bool $value) 设置卡片组是否可选
 * @method $this multiple(bool $value) 设置卡片组是否为多选
 * @method $this checkOnItemClick(bool $value) 设置点选卡片内容是否选中卡片
 */
class Cards extends BaseSchema
{
    public string $type = 'cards';
}
