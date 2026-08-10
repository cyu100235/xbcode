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
namespace plugin\xbCode\builder\Renders\crud\column;

use plugin\xbCode\builder\Components\Action\UrlAction;
use plugin\xbCode\builder\Components\Action\LinkAction;
use plugin\xbCode\builder\Components\Action\AjaxAction;
use plugin\xbCode\builder\Components\Table\TableColumn;
use plugin\xbCode\builder\Components\Action\DialogAction;
use plugin\xbCode\builder\Components\Action\DrawerAction;
use plugin\xbCode\builder\Components\Action\DownloadAction;
use plugin\xbCode\builder\Components\Tpl;

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
     * @param string $label
     * @param string $url
     * @param callable|array $option
     * @return TableColumn|DialogAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnDialog(string $name, string $label, string $url, callable|array $option = [])
    {
        $url = $this->getRightActionAPI($url, 'get');
        /** @var TableColumn|DialogAction */
        $component = $this->useCustomColumn(DialogAction::class, $name, $label, $option);
        $component->vAlign('middle');
        $component->level('link');
        $component->actionType('dialog');
        $component->dialogModel($url, $label, $option);
        $title = $option['title'] ?? "\${{$name}}";
        $html = <<<HTML
        <span class="text-primary cursor-pointer" style="font-size: 12px;">$title</span>
        HTML;
        $component->body([
            [
                'type' => 'tpl',
                'tpl' => $html,
            ],
        ]);
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
    public function addColumnDownload(string $name, string $title, string $url, array $option = [])
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
     * @param string $name 列字段
     * @param string $title 列标题
     * @param string|array $url API地址
     * @param string $content 确认框内容
     * @param string $cTitle 确认框标题
     * @param array $option
     * @return TableColumn|AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnConfirm(string $name, string $title, string|array $url, string $content = '是否确认操作该数据？', string $cTitle = '温馨提示', array $option = [])
    {
        // 重组API地址
        $url = $this->getRightActionAPI($url, 'get');
        /** @var TableColumn|AjaxAction */
        $component = $this->useCustomColumn(AjaxAction::class, $name, $title, $option);
        $component->align('center');
        $component->vAlign('middle');
        $component->level('link');
        // 添加确认框
        $component->api($url);
        $component->confirmTitle($cTitle);
        $component->confirmText($content);
        // 按钮设置
        $buttonTitle = $option['title'] ?? '立即操作';
        // 重设单元格内容
        $html = <<<HTML
        <span class="text-primary cursor-pointer" style="font-size: 12px;">$buttonTitle</span>
        HTML;
        $tplComponent = Tpl::make();
        $tplComponent->tpl($html);
        $component->body([$tplComponent]);
        // 导出组件
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
    public function addColumnLink(string $name, string $title, string $url = '', callable|array $option = [])
    {
        /** @var TableColumn|LinkAction */
        $component = $this->useCustomColumn(new LinkAction($this->url), $name, $title, $option);
        $component->vAlign('middle');
        $component->level('link');
        $title = $option['title'] ?? "\${{$name}}";
        $html = <<<HTML
        <span class="text-primary cursor-pointer" style="font-size: 12px;">$title</span>
        HTML;
        $component->body([
            [
                'type' => 'tpl',
                'tpl' => $html,
            ],
        ]);
        // 设置链接地址
        $component->link($url ? $url : '${' . $name . '}');
        // 返回组件
        return $component;
    }

    /**
     * 添加URL按钮列
     * @param string $name 列字段
     * @param string $label 列标题
     * @param string $url URL地址
     * @param array $option 列配置项
     * - `title` 点击访问的标题
     * @return TableColumn|UrlAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnUrl(string $name, string $label, string $url = '', array $option = [])
    {
        /** @var TableColumn|UrlAction */
        $component = $this->useCustomColumn(UrlAction::class, $name, $label, $option);
        $component->vAlign('middle');
        $component->level('link');
        if ($url) {
            $component->url($url);
        } else {
            $component->url('${' . $name . '}');
        }
        $title = $option['title'] ?? '点击访问';
        $html = <<<HTML
        <a href="\${$name}" class="text-primary cursor-pointer" style="font-size: 12px;">$title</a>
        HTML;
        $tplComponent = Tpl::make();
        $tplComponent->tpl($html);
        $component->body([$tplComponent]);
        return $component;
    }
    /**
     * 添加API请求成功后跳转
     * @param string $name 列字段
     * @param string $label 列标题
     * @param string $url API请求地址
     * @param array $option 列配置项
     * - `title` 点击访问的标题，默认：立即打开
     * - `apiUrlField` 返回数据中URL字段名，默认：url
     * - `blank` 是否新标签页打开，默认：true
     * @return TableColumn|AjaxAction
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumnApiUrl(string $name, string $label, string $url, array $option = [])
    {
        $url = $this->getRightActionAPI($url, 'get');
        /** @var TableColumn|AjaxAction */
        $component = $this->useCustomColumn(AjaxAction::class, $name, $label, $option);
        $component->align('center');
        $component->vAlign('middle');
        $component->level('link');
        $component->actionType('ajax');

        // 从返回数据中获取URL的字段名，默认使用 url
        $apiUrlField = $option['apiUrlField'] ?? 'url';
        // 点击按钮显示的标题
        $title = $option['title'] ?? '立即打开';
        // 是否新标签页打开
        $blank = $option['blank'] ?? true;

        // 构建显示的HTML
        $html = <<<HTML
        <span class="text-primary cursor-pointer" style="font-size: 12px;">$title</span>
        HTML;
        $tplComponent = Tpl::make();
        $tplComponent->tpl($html);
        $component->body([$tplComponent]);

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

        return $component;
    }
}
