<?php
namespace plugin\xbCode\builder\Renders;

use JsonSerializable;
use plugin\xbCode\builder\Components\CRUD;
use plugin\xbCode\builder\Components\Form\AmisForm;
use plugin\xbCode\builder\Components\Nav;
use plugin\xbCode\builder\Components\Page;
use plugin\xbCode\builder\Renders\Common\Router;
use plugin\xbCode\builder\Renders\Grid\GridCurd;
use plugin\xbCode\builder\Renders\Grid\HeaderView;
use plugin\xbCode\builder\Renders\Grid\GridSidebar;
use plugin\xbCode\builder\Components\Form\InputTree;
use plugin\xbCode\builder\Renders\Common\HeaderToolbar;

/**
 * 增删改查表格构建器
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
class Grid implements JsonSerializable
{
    // 页面头部工具
    use HeaderToolbar;
    // 页面头部组件
    use HeaderView;
    // 实现侧边栏
    use GridSidebar;
    // 实现CURD组件
    use GridCurd;

    /**
     * 页面组件
     * @var Page
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
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
     * 侧边栏导航
     * @var Nav
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Nav $navs;

    /**
     * 侧边栏导航默认选中
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $navOption = [
        'field' => '',
        'url' => '',
    ];

    /**
     * 构造函数
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function __construct()
    {
        $this->page = Page::make();
        $this->router = Router::make();

        /** @var InputTree */
        $inputTree = InputTree::make();
        $inputTree->name('sidebar');
        $inputTree->submitOnChange(true);
        $inputTree->selectFirst(true);
        $this->inputTree = $inputTree;
        /** @var Nav */
        $navs = Nav::make();
        $navs->name('navs');
        $navs->stacked(true);
        $this->navs = $navs;

        $limit = 30;
        // 创建CRUD组件
        $crud = CRUD::make();
        // 设置有边框
        $crud->bordered(true);
        // 设置每页显示条数
        $crud->perPage($limit);
        // 用来控制从第几行开始懒渲染行
        $crud->lazyRenderAfter($limit);
        // 设置分页页码字段名
        $crud->pageField('page');
        // 设置分页一页显示的多少条数据的字段名
        $crud->perPageField('limit');
        $this->crud = $crud;
    }

    /**
     * 创建表格实例
     * @param string|array $api
     * @param callable $fun
     * @return Grid
     */
    public static function make(string|array $api, callable $fun)
    {
        $grid = new static;
        $api = $grid->router->getListUrl($api);
        $grid->useCRUD()->api($api);
        $grid->useCRUD()->syncLocation(true);
        $fun($grid);
        return $grid;
    }

    /**
     * 设置组件属性
     * @param string $name
     * @param mixed $value
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setConfig(string $name, mixed $value)
    {
        $this->useCRUD()->setVariable($name, $value);
    }

    /**
     * 获取页面组件
     * @return Page
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function usePage(): Page
    {
        return $this->page;
    }

    /**
     * 获取路由组件
     * @return Router
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useRouter(): Router
    {
        return $this->router;
    }

    /**
     * 使用侧边栏导航
     * @param string $url 页面地址
     * @param string $active 字段名称
     * @return Nav
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useNavs(string $url, string $active = '_nav'): Nav
    {
        $this->navOption['url'] = $url;
        $this->navOption['active'] = $active;
        return $this->navs;
    }

    /**
     * 获取JSON序列化数据
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     * @return array|Page
     */
    public function jsonSerialize(): mixed
    {
        $page = $this->page;
        $toolbar = $this->renderHeaderToolbar();
        if ($toolbar) {
            $page->toolbar($toolbar);
        }
        // 是否有侧边栏导航
        if ($this->navs->links) {
            $navs = $this->navs->links;
            $navUrl = $this->navOption['url'];
            $active = $this->navOption['active'] ?? 'active';
            $navPath = parse_url($navUrl, PHP_URL_PATH);

            // 地址栏参数转数组
            $queryString = parse_url($navUrl, PHP_URL_QUERY);
            parse_str($queryString, $query);
            // 当前选中的值
            $activeValue = $query[$active] ?? '';
            unset($query[$active]);

            // 将参数重新转为字符串
            $query = http_build_query($query);
            $query = urldecode($query);
            
            // 重新处理
            $navs = array_map(function ($item) use ($active, $activeValue, $navPath, $query) {
                $value = $item['value'] ?? '';
                $item['active'] = $value === $activeValue ? true : false;
                $to = "{$navPath}?{$active}={$value}&{$query}";
                $item['to'] = $to;
                $item['icon'] = $item['icon'] ?? 'fa-regular fa-folder-open';
                return $item;
            }, $navs);
            $this->navs->links($navs);
            $page->aside([
                $this->navs
            ]);
        }
        // 是否有选项卡

        // 设置页面内容
        $page->body([
            // 实现表格头部
            $this->renderHeaderView(),
            // 实现CURD表格区域
            $this->renderCURDView(),
        ]);
        return $page;
    }
}
