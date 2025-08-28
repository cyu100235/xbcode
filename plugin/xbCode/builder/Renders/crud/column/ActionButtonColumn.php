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
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Action\UrlAction;
use plugin\xbCode\builder\Components\Action\LinkAction;
use plugin\xbCode\builder\Components\Table\TableColumn;
use plugin\xbCode\builder\Components\Action\DialogAction;
use plugin\xbCode\builder\Components\Action\DrawerAction;
use plugin\xbCode\builder\Components\Action\DownloadAction;

/**
 * 行为按钮列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ActionButtonColumn
{    
    /**
     * 添加弹窗按钮列
     * @param string $name
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnDialog(string $name, string $title, string $url, callable|array $option = [])
    {
        $url = $this->getRightActionAPI($url, 'get');
        $component = $this->createButtonDialog($title, $url, $option);
        $component->level('link');
        /** @var TableColumn|DialogAction */
        $component = $this->useCustomColumn($component, $name, $title, $option);
        $component->align('center');
        $component->vAlign('middle');
        $component->level('link');
        return $component;
    }
    
    /**
     * 添加下载请求按钮列
     * @param string $name
     * @param string $title
     * @param string $url
     * @param array $option
     * @return DownloadAction|TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnDownload(string $name,string $title, string $url, array $option = [])
    {
        $url = $this->getRightActionAPI($url);
        $component = $this->createButtonDownload($title, $url);
        $component->level('link');
        /** @var TableColumn|DownloadAction */
        $component = $this->useCustomColumn($component, $name, $title, $option);
        $component->align('center');
        $component->vAlign('middle');
        $component->level('link');
        return $component;
    }
    
    /**
     * 添加抽屉按钮列
     * @param string $name
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return DrawerAction|TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnDrawer(string $name, string $title, string $url, callable|array $option = [])
    {
        $url = $this->getRightActionAPI($url, 'get');
        $component = $this->createButtonDrawer($title, $url, $option);
        $component->level('link');
        /** @var TableColumn|DrawerAction */
        $component = $this->useCustomColumn($component, $name, $title, $option);
        $component->align('center');
        $component->vAlign('middle');
        $component->level('link');
        return $component;
    }
    
    /**
     * 添加确认框按钮列
     * @param string $name
     * @param string $title
     * @param string $url
     * @param string $content
     * @param string $cTitle
     * @param array $option
     * @return DrawerAction|TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnConfirm(string $name, string $title, string $url, string $content = '是否确认操作该数据？', string $cTitle = '温馨提示', array $option = [])
    {
        $url = $this->getRightActionAPI($url, 'get');
        $component = $this->createButtonConfirm($title, $url, $content, $cTitle);
        $component->level('link');
        $component->className('text-danger');
        /** @var TableColumn|DrawerAction */
        $component = $this->useCustomColumn($component, $name, $title, $option);
        $component->align('center');
        $component->vAlign('middle');
        $component->level('link');
        return $component;
    }
    
    /**
     * 添加链接按钮列
     * @param string $name
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return LinkAction|TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnLink(string $name, string $title, string $url, callable|array $option = [])
    {
        /** @var TableColumn|LinkAction */
        $component = $this->useCustomColumn(LinkAction::class, $name, $title, $option);
        $component->align('center');
        $component->vAlign('middle');
        $component->level('link');
        $component->link('${' . $name . '}');
        return $component;
    }
    
    /**
     * 添加URL按钮列
     * @param string $name
     * @param string $title
     * @param string $url
     * @param bool $target
     * @param array $option
     * @return TableColumn|UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnUrl(string $name,string $title, bool $target = true, array $option = [])
    {
        /** @var TableColumn|UrlAction */
        $component = $this->useCustomColumn(UrlAction::class, $name, $title, $option);
        $component->align('center');
        $component->vAlign('middle');
        $component->level('link');
        $component->url('${' . $name . '}');
        return $component;
    }
}
