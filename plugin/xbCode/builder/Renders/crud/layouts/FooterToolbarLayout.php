<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud\layouts;

use plugin\xbCode\builder\Components\CRUD;

/**
 * 底部工具栏
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FooterToolbarLayout
{

    /**
     * 表格实例
     * @var CRUD
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected CRUD $crud;

    /**
     * 底部工具栏组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $footerToolbar = [];

    /**
     * 分页组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $pagination = [
        'type' => 'pagination',
        'align' => 'right',
        'layout' => 'total,perPage,pager,go',
        'mode' => 'normal',
        'showPerPage' => true,
        'showPageInput' => true,
        'total' => '${total}',
        'perPageAvailable' => [30, 50, 100, 300, 500, 1000],
    ];

    /**
     * 底部批量操作按钮
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $bulkActions = [];

    /**
     * 是否正在收集批量下拉菜单按钮
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $bulkDropdown = false;

    /**
     * 批量下拉菜单按钮列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $bulkDropdownActions = [];

    /**
     * 添加底部批量操作下拉按钮
     * 回调内通过 addBulkAction* 添加的按钮会收进下拉菜单，鼠标移入按钮即展开全部菜单项
     * @param string $title 按钮文本
     * @param callable $callback 回调函数，参数为当前构建器
     * @return \plugin\xbCode\builder\Components\DropdownButton
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionDropdown(string $title, callable $callback)
    {
        /** @var \plugin\xbCode\builder\Components\DropdownButton */
        $component = $this->createButtonDropdown($title);
        // 鼠标移入即展开菜单
        $component->trigger('hover');
        $component->className('xb-bulk-dropdown');
        // 开启下拉收集，回调内的批量按钮改为收进菜单
        $this->bulkDropdown = true;
        $this->bulkDropdownActions = [];
        $callback($this);
        $this->bulkDropdown = false;
        $component->buttons($this->normalizeBulkDropdownButtons($this->bulkDropdownActions));
        $this->bulkActions[] = $component;
        return $component;
    }

    /**
     * 收集批量操作按钮（下拉收集开启时收进下拉菜单，否则作为独立的底部按钮）
     * @param mixed $component 按钮组件
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function addBulkActionButton(mixed $component)
    {
        if ($this->bulkDropdown) {
            $this->bulkDropdownActions[] = $component;
        } else {
            $this->bulkActions[] = $component;
        }
        return $component;
    }

    /**
     * 转换下拉菜单内的批量按钮样式
     * 下拉菜单项自带整行样式，按钮的级别与配色会让整条菜单变成实心色块，
     * 因此这里去掉按钮级别和纯配色类名，保持菜单项样式统一
     * @param array $buttons 按钮组件列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function normalizeBulkDropdownButtons(array $buttons)
    {
        foreach ($buttons as $button) {
            if (!is_object($button)) {
                continue;
            }
            $className = trim(str_replace('text-white', '', (string) $button->getVariable('className')));
            if ($className === '') {
                unset($button->className);
            } else {
                $button->className($className);
            }
            unset($button->level);
        }
        return $buttons;
    }

    /**
     * 添加批量操作弹窗按钮
     * @param string $title
     * @param string $url
     * @param string $method
     * @param callable|array $option
     * @return \plugin\xbCode\builder\Components\Action\DialogAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionDialog(string $title, string $url, string $method = 'GET', callable|array $option = [])
    {
        $url = $this->getBulkActionAPI($url, $method);
        $component = $this->createButtonDialog($title, $url, $option);
        return $this->addBulkActionButton($component);
    }

    /**
     * 添加底部下载请求按钮
     * @param string $title
     * @param string $url
     * @param string $method
     * @return \plugin\xbCode\builder\Components\Action\DownloadAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionDownload(string $title, string $url, string $method = 'GET')
    {
        $url = $this->getBulkActionAPI($url, $method);
        $component = $this->createButtonDownload($title, $url);
        return $this->addBulkActionButton($component);
    }

    /**
     * 添加底部抽屉按钮
     * @param string $title
     * @param string $url
     * @param string $method
     * @param callable|array $option
     * @return \plugin\xbCode\builder\Components\Action\DrawerAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionDrawer(string $title, string $url, string $method = 'GET', callable|array $option = [])
    {
        $url = $this->getBulkActionAPI($url, $method);
        $component = $this->createButtonDrawer($title, $url, $option);
        return $this->addBulkActionButton($component);
    }

    /**
     * 添加底部确认框按钮
     * @param string $title 标题
     * @param string $url 请求地址
     * @param array $option 列配置项
     * @return \plugin\xbCode\builder\Components\Action\AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionConfirm(string $title, string $url, array $option = [])
    {
        $confirmTitle = $option['title'] ?? '温馨提示';
        $confirmContent = $option['content'] ?? '是否确认操作该数据？';
        $method = $option['method'] ?? 'GET';
        $url = $this->getBulkActionAPI($url, $method);
        $component = $this->createButtonConfirm($title, $url, $confirmContent, $confirmTitle);
        $this->crud->selectable(true);
        $this->crud->multiple(true);
        return $this->addBulkActionButton($component);
    }

    /**
     * 添加底部链接按钮
     * @param string $title
     * @param string $url
     * @return \plugin\xbCode\builder\Components\Action\LinkAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionLink(string $title, string $url)
    {
        $url = $this->getBulkActionAPI($url, '');
        $component = $this->createButtonLink($title, $url);
        return $this->addBulkActionButton($component);
    }

    /**
     * 添加底部URL按钮
     * @param string $title
     * @param string $url
     * @param bool $target
     * @return \plugin\xbCode\builder\Components\Action\UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionUrl(string $title, string $url, bool $target = true)
    {
        $url = $this->getBulkActionAPI($url, '');
        $component = $this->createButtonUrl($title, $url, $target);
        return $this->addBulkActionButton($component);
    }

    /**
     * 获取批量操作API地址
     * @param string $url
     * @param string $method
     * @throws \Exception
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getBulkActionAPI(string $url, string $method = 'GET')
    {
        $urls = parse_url($url);
        $path = $urls['path'] ?? '';
        $query = $urls['query'] ?? '';
        if (empty($path)) {
            throw new \Exception('请设置正确的右侧操作API地址');
        }
        parse_str($query, $params);
        // 重新组装URL
        $querys = [
            ...$params,
            'ids' => '${ids|raw}',
        ];
        $path = $method ? "{$method}:{$path}" : $path;
        $url = "{$path}?" . urldecode(http_build_query($querys));
        // 返回URL
        return $url;
    }

    /**
     * 获取底部批量操作按钮
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getBulkActions()
    {
        return $this->bulkActions;
    }

    /**
     * 获取底部工具栏组件
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getFooterToolbar()
    {
        // 检测是否已存在批量操作按钮
        $state = array_filter($this->footerToolbar, function ($item) {
            if (is_array($item)) {
                return isset($item['type']) && $item['type'] === 'bulkActions';
            }
            return $item->type === 'bulkActions';
        });
        if (empty($state)) {
            $this->footerToolbar[] = [
                'type' => 'bulkActions',
                'align' => 'center',
            ];
        }
        // 设置分页组件
        $this->footerToolbar[] = $this->pagination;
        // 返回批量操作按钮
        return $this->footerToolbar;
    }
}
