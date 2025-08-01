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
namespace plugin\xbCode\builder\Renders\crud;

use plugin\xbCode\builder\Components\Action\UrlAction;
use plugin\xbCode\builder\Components\Action\LinkAction;
use plugin\xbCode\builder\Components\Action\AjaxAction;
use plugin\xbCode\builder\Components\Action\CopyAction;
use plugin\xbCode\builder\Components\Action\EmailAction;
use plugin\xbCode\builder\Components\Action\DialogAction;
use plugin\xbCode\builder\Components\Action\DrawerAction;
use plugin\xbCode\builder\Components\Action\ReloadAction;
use plugin\xbCode\builder\Components\Action\DownloadAction;
use plugin\xbCode\builder\Components\Service;

/**
 * 行为按钮组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ButtonUtil
{
    /**
     * 创建请求行为按钮
     * @return AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createButtonAjax()
    {
        $component = new AjaxAction;
        return $component;
    }

    /**
     * 创建确认行为按钮
     * @param string $title
     * @param string $url
     * @param string $content
     * @param string $cTitle
     * @return AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createButtonConfirm(string $title, string $url, string $content, string $cTitle)
    {
        $component = $this->createButtonAjax();
        $component->label($title);
        $component->api($this->APIURL($url));
        $component->confirmTitle($cTitle);
        $component->confirmText($content);
        return $component;
    }

    /**
     * 创建模态框行为按钮
     * @param string $title
     * @param string $url
     * @param array $option
     * @return DialogAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createButtonDialog(string $title, string $url, callable|array $option = [])
    {
        $component = new DialogAction;
        $component->level('primary');
        $component->label($title);
        $dialog = [
            'title' => $title,
            'size' => 'md',
            ...$option,
            'body' => Service::make()->schemaApi($this->APIURL($url)),
        ];
        $component->dialog($dialog);
        return $component;
    }

    /**
     * 创建复制行为按钮
     * @return CopyAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createButtonCopy()
    {
        $component = new CopyAction;
        $component->level('primary');
        return $component;
    }

    /**
     * 创建下载行为按钮
     * @param string $title
     * @param string $url
     * @return DownloadAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createButtonDownload(string $title, string $url)
    {
        $component = new DownloadAction;
        $component->level('primary');
        $component->label($title);
        $component->api($this->APIURL($url));
        return $component;
    }

    /**
     * 创建抽屉行为按钮
     * @param string $title
     * @param string $url
     * @param array $option
     * @return DrawerAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createButtonDrawer(string $title, string $url, callable|array $option = [])
    {
        $component = new DrawerAction;
        $component->level('primary');
        $component->label($title);
        $dialog = [
            'title' => $title,
            'size' => 'md',
            ...$option,
            'body' => Service::make()->schemaApi($this->APIURL($url)),
        ];
        $component->drawer($dialog);
        return $component;
    }

    /**
     * 创建邮件行为按钮
     * @return EmailAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createButtonEmail()
    {
        $component = new EmailAction;
        $component->level('primary');
        return $component;
    }

    /**
     * 创建刷新行为按钮
     * @return ReloadAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createButtonReload()
    {
        $component = new ReloadAction;
        return $component;
    }

    /**
     * 创建链接行为按钮
     * @param string $title
     * @param string $url
     * @return LinkAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createButtonLink(string $title, string $url)
    {
        $component = new LinkAction;
        $component->level('primary');
        $component->label($title);
        $component->link($url);
        return $component;
    }

    /**
     * 创建URL行为按钮
     * @param string $title
     * @param string $url
     * @param bool $target
     * @return UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function createButtonUrl(string $title, string $url, bool $target = true)
    {
        $component = new UrlAction;
        $component->level('primary');
        $component->label($title);
        $component->url($url);
        $component->blank($target);
        return $component;
    }

    /**
     * 生成API请求URL
     * @param string $url
     * @param array $querys
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function APIURL(string $url, array $querys = [])
    {
        $urls = parse_url($url);
        if (!isset($urls['path'])) {
            throw new \Exception('请设置正确的接口地址');
        }
        $path = $urls['path'];
        $query = $urls['query'] ?? '';
        parse_str($query, $params);
        $params = empty($params) ? [] : $params;
        $params = array_merge($params, $querys);
        $query = http_build_query($params);
        $query = $query ? "?{$query}" : '';
        $url = "GET:{$path}{$query}";
        return $url;
    }
}
