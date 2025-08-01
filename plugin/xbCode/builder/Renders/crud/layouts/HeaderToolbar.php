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
namespace plugin\xbCode\builder\Renders\crud\layouts;

/**
 * 头部左侧工具栏布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait HeaderToolbar
{
    /**
     * 头部工具栏组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $headerToolbar = [];

    /**
     * 添加头部对话框按钮
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return \plugin\xbCode\builder\Components\Action\DialogAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderDialog(string $title, string $url, callable|array $option = [])
    {
        $url = $this->APIURL($url, ['_dialog' => 1]);
        $component = $this->createButtonDialog($title, $url, $option);
        $this->headerToolbar[] = $component;
        return $component;
    }

    /**
     * 添加头部下载请求按钮
     * @param string $title
     * @param string $url
     * @return \plugin\xbCode\builder\Components\Action\DownloadAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderDownload(string $title, string $url)
    {
        $component = $this->createButtonDownload($title, $url);
        $component->label($title);
        $component->api($this->APIURL($url));
        $this->headerToolbar[] = $component;
        return $component;
    }

    /**
     * 添加头部抽屉按钮
     * @param string $title
     * @param string $url
     * @param callable|array $config
     * @return \plugin\xbCode\builder\Components\Action\DrawerAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderDrawer(string $title, string $url, callable|array $option = [])
    {
        $url = $this->APIURL($url, ['_dialog' => 1]);
        $component = $this->createButtonDrawer($title, $url, $option);
        $this->headerToolbar[] = $component;
        return $component;
    }

    /**
     * 添加头部确认框按钮
     * @param string $title
     * @param string $url
     * @param string $content
     * @param string $cTitle
     * @return \plugin\xbCode\builder\Components\Action\AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderConfirm(string $title, string $url, string $content = '是否确认操作该数据？', string $cTitle = '温馨提示')
    {
        $component = $this->createButtonConfirm($title, $url, $content, $cTitle);
        $this->headerToolbar[] = $component;
        return $component;
    }

    /**
     * 添加头部链接按钮
     * @param string $title
     * @param string $url
     * @return \plugin\xbCode\builder\Components\Action\LinkAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderLink(string $title, string $url)
    {
        $component = $this->createButtonLink($title, $url);
        $this->headerToolbar[] = $component;
        return $component;
    }

    /**
     * 添加头部URL按钮
     * @param string $title
     * @param string $url
     * @param bool $target
     * @return \plugin\xbCode\builder\Components\Action\UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderUrl(string $title, string $url, bool $target = true)
    {
        $component = $this->createButtonUrl($title, $url, $target);
        $this->headerToolbar[] = $component;
        return $component;
    }
}
