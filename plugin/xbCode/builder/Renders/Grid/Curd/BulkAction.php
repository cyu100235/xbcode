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
namespace plugin\xbCode\builder\Renders\Grid\Curd;

use plugin\xbCode\builder\Components\Service;
use plugin\xbCode\builder\Components\Action\UrlAction;
use plugin\xbCode\builder\Components\Action\LinkAction;
use plugin\xbCode\builder\Components\Action\AjaxAction;
use plugin\xbCode\builder\Components\Action\DialogAction;
use plugin\xbCode\builder\Components\Action\DrawerAction;
use plugin\xbCode\builder\Components\Action\DownloadAction;

/**
 * 工具按钮行为
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait BulkAction
{
    /**
     * CURD头部工具栏
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $gridHeaderToolbar = [];

    /**
     * CRUD头部工具栏按钮列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $bulkActions = [];

    /**
     * 开启工具栏按钮-网络请求
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionAjax(string $title,string $url, callable|array $option = null): AjaxAction
    {
        $component = new AjaxAction;
        $component->api($url);
        $component->label($title);
        $this->setBulkActionComponent($component, $option);
        return $component;
    }

    /**
     * 开启工具栏按钮-确认框
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionConfirm(string $title,string $url, callable|array $option = null): AjaxAction
    {
        $component = new AjaxAction;
        $component->api($url);
        $component->label($title);
        $component->confirmTitle('温馨提示');
        $component->confirmText('是否确认进行该操作？');
        $this->setBulkActionComponent($component, $option);
        return $component;
    }

    /**
     * 开启工具栏按钮-弹窗
     * @param string $url
     * @param string $title
     * @param callable|array $option
     * @return DialogAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionDialog(string $title,string $url, callable|array $option = null): DialogAction
    {
        /** @var DialogAction */
        $component = new DialogAction;
        $component->label($title);
        $component->level('primary');
        // 设置弹窗属性
        $this->setBulkActionComponent($component, $option);
        // 设置弹窗标题和内容
        $component->dialog([
            'title' => $title,
            'body' => Service::make()->schemaApi($url),
        ]);
        return $component;
    }

    /**
     * 开启工具栏按钮-下载
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return DownloadAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionDownload(string $title,string $url, callable|array $option = null): DownloadAction
    {
        /** @var DownloadAction */
        $component = new DownloadAction;
        $component->api($url);
        $component->label($title);
        $this->setBulkActionComponent($component, $option);
        return $component;
    }

    /**
     * 开启工具栏按钮-抽屉
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return DrawerAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionDrawer(string $title,string $url, callable|array $option = null): DrawerAction
    {
        $component = new DrawerAction;
        $component->label($title);
        $this->setBulkActionComponent($component, $option);
        // 设置抽屉属性
        $component->drawer([
            'title' => $title,
            'body' => Service::make()->schemaApi($url),
        ]);
        return $component;
    }

    /**
     * 开启工具栏按钮-内部跳转
     * @param string $url
     * @param string $title
     * @param callable|array $option
     * @return LinkAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionLink(string $title,string $url, callable|array $option = null): LinkAction
    {
        $component = new LinkAction;
        $component->link($url);
        $component->label($title);
        $this->setBulkActionComponent($component, $option);
        return $component;
    }

    /**
     * 开启工具栏按钮-直接跳转
     * @param string $url
     * @param string $title
     * @param callable|array $option
     * @return UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addBulkActionUrl(string $title,string $url, callable|array $option = null): UrlAction
    {
        $component = new UrlAction;
        $component->url($url);
        $component->label($title);
        $this->setBulkActionComponent($component, $option);
        return $component;
    }

    /**
     * 动态设置组件属性
     * @param mixed $component
     * @param callable|array $option
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function setBulkActionComponent($component, callable|array $option = null): void
    {
        $this->setComponent($component, $option);
        $this->bulkActions[] = $component;
        $this->addToolbar('left', 'bulkActions');
    }
}
