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
namespace plugin\xbCode\builder\Renders\Grid;

use plugin\xbCode\builder\Renders\Grid;
use plugin\xbCode\builder\Components\Page;
use plugin\xbCode\builder\Components\CRUD;
use plugin\xbCode\builder\Renders\Common\Router;
use plugin\xbCode\builder\Renders\Grid\Column\Column;
use plugin\xbCode\builder\Renders\Grid\Curd\GridColumn;
use plugin\xbCode\builder\Renders\Grid\Curd\GridFilter;
use plugin\xbCode\builder\Renders\Grid\Curd\GridFooter;
use plugin\xbCode\builder\Renders\Grid\Curd\GridQuickEdit;
use plugin\xbCode\builder\Renders\Grid\Curd\GridToolbar;
use plugin\xbCode\builder\Renders\Grid\Curd\GridActionButtons;

/**
 * 表格增删改查功能
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait GridCurd
{
    // 快速编辑
    use GridQuickEdit;
    // 筛选查询
    use GridFilter;
    // 表格工具栏
    use GridToolbar;
    // 表格列组件
    use GridColumn;
    // 表格操作按钮
    use GridActionButtons;
    // 表格底部工具栏
    use GridFooter;

    /**
     * 页面实例
     * @var Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Page $page;

    /**
     * 路由组件
     * @var Router
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Router $router;

    /**
     * 表格实例
     * @var Grid
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    protected Grid $grid;

    /**
     * CURD组件
     * @var CRUD
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected CRUD $crud;

    /**
     * CURD组件名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private string $crudName = "crud";

    /**
     * 筛选查询列表
     * @var array
     */
    protected array $filter = [];

    /**
     * 表格列配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $columns = [];

    /**
     * 表格头部工具栏
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $gridHeaderToolbar = [];

    /**
     * CURD头部工具栏按钮列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $bulkActions = [];

    /**
     * 表格底部工具栏
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $gridFooterToolbar = [];

    /**
     * 使用CURD组件
     * @return CRUD
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useCRUD()
    {
        return $this->crud;
    }
    
    /**
     * 初始化默认表格
     * @param string $api
     * @param string $title
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function initCRUD(string $title = '')
    {
        // 页面标题
        $this->usePage()->title($title);
        // 是否将筛选查询的参数同步到地址栏
        $this->useCRUD()->syncLocation(true);
        // 展示列显示开关, 自动即：列数量大于或等于 5 个时自动开启
        $this->useCRUD()->columnsTogglable(true);
        // 开启表格列拖拽排序
        $this->useCRUD()->draggable(true);
        // 始终显示分页
        $this->useCRUD()->alwaysShowPagination(true);
        // 开启表格列设置
        $this->toolbarColumns();
        // 开启表格拖拽排序
        $this->toolbarDrag();
        // 开启表格刷新按钮
        $this->toolbarReload();
        // 开启表格分页
        $this->toolbarPage();
    }

    /**
     * 获取CURD组件名称
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getCrudName(): string
    {
        return $this->crudName;
    }

    /**
     * 设置组件名称
     * @param string $crudName
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setCrudName(string $crudName): void
    {
        $this->crudName = $crudName;
    }

    /**
     * 渲染表格
     * @return CRUD
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function renderCURDView(): CRUD
    {
        // 设置组件名称
        $crudName = $this->getCrudName();
        $this->crud->name($crudName);
        // 设置表格主键
        $primaryKey = $this->router->getPrimaryKey();
        $this->crud->primaryField($primaryKey);
        // 设置表格样式类名
        if($this->renderHeaderToolbar()){
            $this->crud->className('mt-2');            
        }
        // 设置表格自适应高度
        $this->crud->autoFillHeight(true);

        // 获取并设置筛选查询
        $filter = $this->renderCURDFilter();
        if (count($filter)) {
            $this->crud->filter($filter);
        }

        // 获取并设置表格头部工具栏
        $headerToolbar = $this->gridHeaderToolbar;
        if (count($headerToolbar) > 0) {
            $this->crud->headerToolbar($headerToolbar);
        }
        // 获取并设置表格底部工具栏
        $footerToolbar = $this->gridFooterToolbar;
        if (count($footerToolbar) > 0) {
            $this->crud->footerToolbar($footerToolbar);
        }

        // 获取并设置表格头部工具栏按钮块
        $bulkActions = $this->bulkActions;
        if (count($bulkActions) > 0) {
            $this->crud->bulkActions($bulkActions);
        }

        // 获取表格列配置
        $columns = array_map(function ($column) {
            /** @var Column $column */
            return $column->render();
        }, $this->columns);

        // 获取表格右侧操作按钮
        $actionButtons = $this->renderCURDActionButtons();
        if(count($actionButtons) > 0) {
            $operation = $this->getCRUDConfig();
            $columns[] = array_merge($operation,[
                'type' => 'operation',
                'buttons' => $actionButtons,
            ]);
        }
        $this->crud->columns($columns);
        // 初始化底部工具栏
        $this->initFooterToolbar();
        // 表格底部工具栏
        $footerToolbar = $this->renderFooterToolbar();
        $this->crud->footerToolbar($footerToolbar);

        // 返回CURD组件
        return $this->crud;
    }
}
