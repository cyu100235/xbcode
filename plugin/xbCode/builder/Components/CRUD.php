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

use plugin\xbCode\builder\Components\Table\Table;

/**
 * CRUD 渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/crud
 * @method $this model(string $value) 设置表格模式
 * @method $this title(string $value) 设置标题
 * @method $this bordered(bool $value) 设置是否有边框
 * @method $this expandConfig(array $value)  设置展开行配置，参数示例：expand，expandAll，accordion
 * @method $this className(string $value) 设置表格外层 Dom 的类名
 * @method $this api(mixed $value) 设置 CRUD 用来获取列表数据的 api
 * @method $this deferApi(string $value) 当行数据中有 defer 属性时，用此接口进一步加载内容
 * @method $this loadDataOnce(bool $value) 是否一次性加载所有数据（前端分页）
 * @method $this loadDataOnceFetchOnFilter(bool $value) 在开启 loadDataOnce 时，filter 时是否去重新请求 api
 * @method $this source(string $value) 数据映射接口某字段的值，不设置默认使用接口返回的${items}或者${rows}，可以设置成上层数据源内容
 * @method $this filter(mixed $value) 设置过滤器，当该表单提交后，会把数据带给当前 mode 刷新列表
 * @method $this filterTogglable(bool|array $value) 是否可显隐过滤器
 * @method $this filterDefaultVisible(bool $value) 设置过滤器默认是否可见
 * @method $this initFetch(bool $value) 是否初始化的时候拉取数据, 只针对有 filter 的情况, 没有 filter 初始都会拉取数据
 * @method $this interval(int $value) 刷新时间(最低 1000)
 * @method $this silentPolling(bool $value) 配置刷新时是否隐藏加载动画
 * @method $this stopAutoRefreshWhen(string $value) 通过表达式来配置停止刷新的条件
 * @method $this stopAutoRefreshWhenModalIsOpen(bool $value) 当有弹框时关闭自动刷新，关闭弹框又恢复
 * @method $this syncLocation(bool $value) 是否将过滤条件的参数同步到地址栏
 * @method $this draggable(bool $value) 是否可通过拖拽排序
 * @method $this resizable(bool $value) 是否可以调整列宽度
 * @method $this itemDraggableOn(string $value) 用表达式来配置是否可拖拽排序
 * @method $this saveOrderApi(string|array $api) 保存排序的 api
 * @method $this quickSaveApi(string|array $api) 快速编辑后用来批量保存的 API
 * @method $this quickSaveItemApi(string|array $api) 快速编辑配置成及时保存时使用的 API
 * @method $this bulkActions(array $value) 批量操作列表，配置后，表格可进行选中操作
 * @method $this messages(array $value) 覆盖消息提示，如果不指定，将采用 api 返回的 message
 * @method $this primaryField(string $value) 设置 ID 字段名
 * @method $this perPage(int $value) 设置一页显示多少条数据
 * @method $this orderBy(string $value) 默认排序字段，这个是传给后端，需要后端接口实现
 * @method $this orderDir(string $value) 排序方向
 * @method $this defaultParams(array $value) 设置默认 filter 默认参数，会在查询的时候一起发给后端
 * @method $this pageField(string $value) 设置分页页码字段名
 * @method $this perPageField(string $value) 设置分页一页显示的多少条数据的字段名。注意：最好与 defaultParams 一起使用
 * @method $this pageDirectionField(string $value) 分页方向字段名可能是 forward 或者 backward
 * @method $this perPageAvailable(array $value) 设置一页显示多少条数据下拉框可选条数
 * @method $this orderField(string $value) 设置用来确定位置的字段名，设置后新的顺序将被赋值到该字段中
 * @method $this hideQuickSaveBtn(bool $value) 隐藏顶部快速保存提示
 * @method $this autoJumpToTopOnPagerChange(bool $value) 当切分页的时候，是否自动跳顶部
 * @method $this syncResponse2Query(bool $value) 将返回数据同步到过滤器上
 * @method $this keepItemSelectionOnPageChange(bool $value) 保留条目选择，默认分页、搜索后，用户选择条目会被清空，开启此选项后会保留用户选择，可以实现跨页面批量操作
 * @method $this maxKeepItemSelectionLength(int $value) 和keepItemSelectionOnPageChange搭配使用，限制最大勾选数
 * @method $this maxItemSelectionLength(int $value) 可单独使用限制当前页的最大勾选数，也可以和keepItemSelectionOnPageChange搭配使用达到和 maxKeepItemSelectionLength 一样的效果
 * @method $this headerToolbar(array $value) 顶部工具栏配置
 * @method $this footerToolbar(array $value) 底部工具栏配置
 * @method $this alwaysShowPagination(bool $value) 是否总是显示分页
 * @method $this affixHeader(bool $value) 是否固定表头(table 下)
 * @method $this affixFooter(bool $value) 是否固定表格底部工具栏
 * @method $this autoGenerateFilter(bool|array $value) 是否开启查询区域，开启后会根据列元素的 searchable 属性值，自动生成查询条件表单
 * @method $this resetPageAfterAjaxItemAction(bool $value) 单条数据 ajax 操作后是否重置页码为第一页
 * @method $this autoFillHeight(bool|array $value) 内容区域自适应高度
 * @method $this canAccessSuperData(bool $value) 指定是否可以自动获取上层的数据并映射到表格数据上，如果列也配置该属性，则列的优先级更高
 * @method $this matchFunc(string $value) 自定义匹配函数, 当开启loadDataOnce时，会基于该函数计算的匹配结果进行过滤，主要用于处理列字段类型较为复杂或者字段值格式和后端返回不一致的场景
 * @method $this parsePrimitiveQuery(array $value) 是否开启 Query 信息转换，开启后将会对 url 中的 Query 进行转换，默认开启，默认仅转化布尔值
 */
class CRUD extends Table
{
    public string $type = 'crud';

    public function __construct()
    {
        $this->columnsTogglable(false);
    }
}
