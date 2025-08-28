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
 * 选项卡组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/tabs
 * @method $this className(string $value) 外层 Dom 的 CSS 类名
 * @method $this defaultKey(string|int $value) 初始化时激活的选项卡
 * @method $this activeKey(string|int $value) 激活的选项卡
 * @method $this linksClassName(string $value) Tabs 标题区的类名
 * @method $this contentClassName(string $value) Tabs 内容区的类名
 * @method $this tabsMode(string $value) 展示模式line、card、radio、vertical、chrome、simple、strong、tiled、sidebar
 * @method $this tabs(array $value) tabs内容
 * @method $this source(string $value) tabs 关联数据，关联后可以重复生成选项卡
 * @method $this toolbar(array $value) tabs 中的工具栏
 * @method $this toolbarClassName(string $value) tabs 中工具栏的类名
 * @method $this mountOnEnter(bool $value) 只有在点中 tab 的时候才渲染
 * @method $this unmountOnExit(bool $value) 切换 tab 的时候销毁
 * @method $this addable(bool $value) 是否支持新增
 * @method $this addBtnText(string $value) 新增按钮文案
 * @method $this closable(bool $value) 是否支持删除
 * @method $this draggable(bool $value) 是否支持拖拽
 * @method $this showTip(bool $value) 是否支持提示
 * @method $this showTipClassName(string $value) 提示的类
 * @method $this editable(bool $value) 是否可编辑标签名
 * @method $this scrollable(bool $value) 是否导航支持内容溢出滚动
 * @method $this sidePosition(string $value) sidebar 模式下，标签栏位置
 * @method $this collapseOnExceed(int $value) 当 tabs 超出多少个时开始折叠
 * @method $this collapseBtnLabel(string $value) 用来设置折叠按钮的文字
 * @method $this swipeable(bool $value) 是否开启手势滑动切换（移动端生效）
 */
class Tabs extends BaseSchema
{
    public string $type = 'tabs';
}
