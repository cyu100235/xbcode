<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud\layouts;

use plugin\xbCode\builder\Components\Action\UrlAction;
use plugin\xbCode\builder\Components\Action\AjaxAction;
use plugin\xbCode\builder\Components\Action\LinkAction;
use plugin\xbCode\builder\Components\Action\DrawerAction;
use plugin\xbCode\builder\Components\Action\DialogAction;
use plugin\xbCode\builder\Components\Action\DownloadAction;

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
     * @return DialogAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderDialog(string $title, string $url, callable|array $option = [])
    {
        $url = $this->APIURL($url);
        /**
         * @var DialogAction
         * @author 楚羽幽 958416459@qq.com
         * @copyright 贵州积木云网络科技有限公司
         */
        $component = $this->createButtonDialog($title, $url, $option);
        $component->setVariable('position', 'top');
        $this->headerToolbar[] = $component;
        return $component;
    }

    /**
     * 添加头部下载请求按钮
     * @param string $title
     * @param string $url
     * @return DownloadAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderDownload(string $title, string $url)
    {
        $url = $this->APIURL($url);
        /**
         * @var DownloadAction
         * @author 楚羽幽 958416459@qq.com
         * @copyright 贵州积木云网络科技有限公司
         */
        $component = $this->createButtonDownload($title, $url);
        $component->actionType('saveAs');
        $component->setVariable('position', 'top');
        $component->label($title);
        $component->api($url);
        $this->headerToolbar[] = $component;
        return $component;
    }

    /**
     * 添加头部抽屉按钮
     * @param string $title
     * @param string $url
     * @param callable|array $option
     * @return DrawerAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderDrawer(string $title, string $url, callable|array $option = [])
    {
        $url = $this->APIURL($url);
        /**
         * @var DrawerAction
         * @author 楚羽幽 958416459@qq.com
         * @copyright 贵州积木云网络科技有限公司
         */
        $component = $this->createButtonDrawer($title, $url, $option);
        $component->setVariable('position', 'top');
        $this->headerToolbar[] = $component;
        return $component;
    }

    /**
     * 添加头部确认框按钮
     * @param string $title
     * @param string $url
     * @param string $option
     * @return AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderConfirm(string $title, string $url, array $option = [])
    {
        $cTitle = $option['title'] ?? '温馨提示';
        $content = $option['content'] ?? '是否确认操作该数据？';
        if (isset($option['title'])) {
            unset($option['title']);
        }
        if (isset($option['content'])) {
            unset($option['content']);
        }
        /**
         * @var AjaxAction
         * @author 楚羽幽 958416459@qq.com
         * @copyright 贵州积木云网络科技有限公司
         */
        $component = $this->createButtonConfirm($title, $url, $content, $cTitle);
        $component->setVariable('position', 'top');
        $this->headerToolbar[] = $component;
        return $component;
    }

    /**
     * 添加头部链接按钮
     * @param string $title
     * @param string $url
     * @return LinkAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderLink(string $title, string $url)
    {
        /** @var LinkAction */
        $component = $this->createButtonLink($title, $url);
        $component->setVariable('position', 'top');
        $this->headerToolbar[] = $component;
        return $component;
    }

    /**
     * 添加头部URL按钮
     * @param string $title
     * @param string $url
     * @param bool $target
     * @return UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderUrl(string $title, string $url, bool $target = true)
    {
        /**
         * @var UrlAction
         * @author 楚羽幽 958416459@qq.com
         * @copyright 贵州积木云网络科技有限公司
         */
        $component = $this->createButtonUrl($title, $url, $target);
        $component->setVariable('position', 'top');
        $this->headerToolbar[] = $component;
        return $component;
    }
    /**
     * 添加头部请求后跳转按钮
     * @param string $title 按钮名称
     * @param string $url API地址
     * @param bool $target 是否新标签页打开
     * @param array $option 组件参数
     * @return UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addHeaderActionApiUrl(string $title, string $url, bool $target = true, array $option = [])
    {
        $url = $this->APIURL($url);
        /** @var UrlAction */
        $component = $this->createButtonUrl($title, '', $target);
        $component->setVariable('position', 'top');
        $component->vAlign('middle');
        $component->actionType('ajax');

        // 从返回数据中获取URL的字段名，默认使用 url
        $apiUrlField = $option['apiUrlField'] ?? 'url';
        // 是否新标签页打开
        $blank = $option['blank'] ?? true;

        // 执行事件 - 先请求API，成功后从返回数据中获取URL并跳转
        $component->onEvent([
            'click' => [
                'actions' => [
                    [
                        'actionType' => 'ajax',
                        'args' => [
                            'api' => $url,
                        ],
                    ],
                    [
                        'actionType' => 'url',
                        'args' => [
                            'url' => '${event.data.' . $apiUrlField . '}',
                            'blank' => $blank,
                        ],
                    ],
                ],
            ],
        ]);
        $this->headerToolbar[] = $component;
        return $component;
    }
}
