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
namespace plugin\xbCode\builder\Renders;

use plugin\xbCode\builder\Components\Nav;
use plugin\xbCode\builder\Components\Page;
use plugin\xbCode\builder\Components\CRUD;
use plugin\xbCode\builder\Renders\crud\ButtonUtil;
use plugin\xbCode\builder\Components\Form\AmisForm;
use plugin\xbCode\builder\Components\Custom\XbTabs;
use plugin\xbCode\builder\Renders\crud\TableCrudBase;
use plugin\xbCode\builder\Renders\crud\layouts\TabsLayout;
use plugin\xbCode\builder\Renders\crud\layouts\CrudLayout;
use plugin\xbCode\builder\Renders\crud\layouts\CountLayout;
use plugin\xbCode\builder\Renders\crud\layouts\FilterLayout;
use plugin\xbCode\builder\Renders\crud\layouts\PromptLayout;
use plugin\xbCode\builder\Renders\crud\layouts\SidebarLayout;
use plugin\xbCode\builder\Renders\crud\layouts\ToolbarLayout;
use plugin\xbCode\builder\Renders\crud\layouts\ComponentLayout;

/**
 * 增删改查表格构建器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class TableCrud extends Base
{
    // 按钮工具类
    use ButtonUtil;
    // 侧边栏布局
    use SidebarLayout;
    // 组件布局
    use ComponentLayout;
    // 统计布局
    use CountLayout;
    // 提示词布局
    use PromptLayout;
    // 选项卡布局
    use TabsLayout;
    // 表单查询布局
    use FilterLayout;
    // 头部工具栏布局
    use ToolbarLayout;
    // 表格布局
    use CrudLayout;
    // 表格基础参数
    use TableCrudBase;

    /**
     * 渲染页面组件
     * @var Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Page $page;

    /**
     * 表格实例
     * @var CRUD
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected CRUD $crud;

    /**
     * 构造函数
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct()
    {
        // 页面组件
        $this->page = new Page;

        // 侧边栏组件
        $navs = new Nav;
        $navs->name('navs');
        $navs->stacked(true);
        $this->navs = $navs;

        // 选项卡组件
        $tabs = new XbTabs;
        $this->tabs = $tabs;

        // 表单查询组件
        $form = new AmisForm;
        $this->form = $form;

        // 表格组件
        $crud = new CRUD;
        $this->crud = $crud;
    }

    /**
     * 初始化组件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function init()
    {
        // 表格ID
        $this->useCRUD()->id('crud');
        // 表格名称
        $this->useCRUD()->name('crud');
        // 设置当前页面完整地址
        $this->useCRUD()->api($this->url);
        // 是否有边框
        $this->useCRUD()->border(true);
        // 设置表格不支持拖拽调整列宽
        $this->useCRUD()->resizable(false);
        // 设置表格自动填充高度
        $this->useCRUD()->autoFillHeight(true);
        // 设置每页显示条数
        $this->useCRUD()->perPage($this->limit);
        // 用来控制从第几行开始懒渲染行
        $this->useCRUD()->lazyRenderAfter($this->limit);
        // 设置分页页码字段名
        $this->useCRUD()->pageField($this->pageField);
        // 是否将过滤条件的参数同步到地址栏
        $this->useCRUD()->syncLocation(true);
        // 设置分页一页显示的多少条数据的字段名
        $this->useCRUD()->perPageField($this->limitField);
        // 开启列设置开关
        $this->useCRUD()->columnsTogglable(true);
        // 设置列配置
        $this->addToolbarColumnsTogglable();
        // 设置表格刷新按钮
        $this->addToolbarReload();
    }

    /**
     * 创建增删改查表格
     * @param callable $func
     * @param string $url
     * @return TableCrud
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make(callable $func, string $url)
    {
        $class = new self;
        $class->setUrl($url);
        $class->init();
        $func($class);
        return $class;
    }

    /**
     * 获取增删改查表格组件实例
     * @return CRUD
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useCRUD()
    {
        return $this->crud;
    }

    /**
     * 获取表格处理后头部工具栏组件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getCrudHeaderToolbar()
    {
        // 获取原始头部工具栏组件
        $headerToolbar = $this->getHeaderToolbar();
        // 获取左侧菜单列表
        $leftToolbar = array_filter($headerToolbar, function ($item) {
            if (is_array($item)) {
                if (!isset($item['align'])) {
                    return true;
                }
                if ($item['type'] === 'bulkActions') {
                    return false;
                }
                return isset($item['align']) && $item['align'] === 'left';
            }
            return isset($item->type) || $item->type === 'left';
        });
        // 获取批量操作按钮
        $bulkActions = array_filter($headerToolbar, function ($item) {
            if (is_array($item)) {
                return isset($item['type']) && $item['type'] === 'bulkActions';
            }
            return isset($item->type) && $item->type === 'bulkActions';
        });
        // 获取右侧工具栏
        $rightToolbar = array_filter($headerToolbar, function ($item) {
            if (is_array($item)) {
                return isset($item['align']) && $item['align'] === 'right';
            }
            return isset($item->type) && $item->type === 'right';
        });
        // 合并左侧菜单、批量操作按钮和右侧工具栏
        $headerToolbar = array_merge($leftToolbar, $bulkActions, $rightToolbar);
        // 返回处理后的头部工具栏组件
        return $headerToolbar;
    }

    /**
     * 获取组件规则
     * @return Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function create():Page
    {
        if (empty($this->url)) {
            throw new \Exception('请设置当前页面完整地址');
        }
        $page = $this->page;
        $body = [];
        // 侧边栏组件
        $sidebar = $this->getSidebar();
        if (!empty($sidebar->links)) {
            $page->aside([
                $sidebar,
            ]);
        }
        // 自定义组件
        $components = $this->getComponents();
        if ($components) {
            $body[] = $components;
        }
        // 数据统计
        $count = $this->getCount();
        if (!empty($count['list'])) {
            $body[] = $count;
        }
        // 提示词
        $prompt = $this->getPrompt();
        if ($prompt) {
            $body[] = $prompt;
        }
        // 选项卡
        $tabs = $this->useTabs();
        if (!empty($tabs->items)) {
            $body[] = $tabs;
        }

        // 表单查询
        $filter = $this->getFilter();
        if ($filter) {
            $this->useCRUD()->filter($filter);
        }

        // 头部工具栏组件
        $headerToolbar = $this->getCrudHeaderToolbar();
        if ($headerToolbar) {
            $this->useCRUD()->headerToolbar($headerToolbar);
        }

        // 底部批量操作按钮
        $bulkActions = $this->getBulkActions();
        if ($bulkActions) {
            $this->useCRUD()->bulkActions($bulkActions);
        }

        // 底部工具栏组件
        $footerToolbar = $this->getFooterToolbar();
        $this->useCRUD()->footerToolbar($footerToolbar);

        // 获取表格列配置
        $columns = $this->getColumns();

        // 设置表格操作列配置
        $actionButtons = $this->getActionButtons();
        if ($actionButtons) {
            $actionConfig = $this->getActionConfig();
            $columns[] = array_merge($actionConfig, [
                'type' => 'operation',
                'buttons' => $actionButtons,
            ]);
        }

        // 设置表格列配置
        $this->useCRUD()->columns($columns);

        // 设置表格边框
        if (isset($this->useCRUD()->border) && $this->useCRUD()->border) {
            // 表格边框
            $this->useCRUD()->className('xb-crud-border');
        }

        // 表格组件
        $crud = $this->useCRUD();
        $body[] = $crud;

        // 追加至主题内容
        $page->body($body);
        // 返回组件实例
        return $page;
    }
}
