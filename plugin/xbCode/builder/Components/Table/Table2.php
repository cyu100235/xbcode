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
namespace plugin\xbCode\builder\Components\Table;

use plugin\xbCode\builder\Components\BaseSchema;

/**
 * 表格加强版组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/table
 * @method $this title(string $value) 标题
 * @method $this source(string $value) 数据源, 绑定当前环境变量
 * @method $this deferApi(string $value) 当行数据中有 defer 属性时，用此接口进一步加载内容
 * @method $this affixHeader(bool $value) 是否固定表头
 * @method $this affixFooter(bool $value) 是否固定表格底部工具栏
 * @method $this columnsTogglable(bool|string $value) 展示列显示开关, 自动即：列数量大于或等于 5 个时自动开启
 * @method $this placeholder(string $value) 当没数据的时候的文字提示
 * @method $this className(string $value) 外层 CSS 类名
 * @method $this showIndex(bool $value) 是否展示序号
 * @method $this tableClassName(string $value) 表格 CSS 类名
 * @method $this headerClassName(string $value) 顶部外层 CSS 类名
 * @method $this footerClassName(string $value) 底部外层 CSS 类名
 * @method $this toolbarClassName(string $value) 工具栏 CSS 类名
 * @method $this columns(array $value) 用来设置列信息
 * @method $this combineNum(int $value) 自动合并单元格
 * @method $this itemActions(array $value) 悬浮行操作按钮组
 * @method $this itemCheckableOn(string $value) 配置当前行是否可勾选的条件，要用 表达式
 * @method $this itemDraggableOn(string $value) 配置当前行是否可拖拽的条件，要用 表达式
 * @method $this checkOnItemClick(bool $value) 点击数据行是否可以勾选当前行
 * @method $this rowClassName(string $value) 给行添加 CSS 类名
 * @method $this rowClassNameExpr(string $value) 通过模板给行添加 CSS 类名
 * @method $this prefixRow(array $value) 顶部总结行
 * @method $this affixRow(array $value) 底部总结行
 * @method $this itemBadge(array $value) 行角标配置
 * @method $this autoFillHeight(bool|array $value) 内容区域自适应高度，可选择自适应、固定高度和最大高度
 * @method $this resizable(bool $value) 列宽度是否支持调整
 * @method $this selectable(bool $value) 支持勾选
 * @method $this multiple(bool $value) 勾选 icon 是否为多选样式checkbox， 默认为radio
 * @method $this lazyRenderAfter(int $value) 用来控制从第几行开始懒渲染行，用来渲染大表格时有用
 * @method $this tableLayout(string $value) 当配置为 fixed 时，内容将不会撑开表格，自动换行
 */
class Table2 extends BaseSchema
{
    public string $type = 'table';
}
