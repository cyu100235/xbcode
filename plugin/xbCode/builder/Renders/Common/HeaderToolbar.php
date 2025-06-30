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
namespace plugin\xbCode\builder\Renders\Common;

use plugin\xbCode\builder\Components\Form\InputFile;
use plugin\xbCode\builder\Components\Service;
use plugin\xbCode\builder\Components\Action\UrlAction;
use plugin\xbCode\builder\Components\Action\LinkAction;
use plugin\xbCode\builder\Components\Action\AjaxAction;
use plugin\xbCode\builder\Components\Action\DialogAction;
use plugin\xbCode\builder\Components\Action\DrawerAction;

/**
 * 页面头部工具栏
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait HeaderToolbar
{
    use ComponentUtils;

    /**
     * 路由组件
     * @var Router
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Router $router;

    /**
     * 页面头部工具栏组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $headerToolbar = [];

    /**
     * 添加头部上传组件
     * @param string $title
     * @param string|array $url
     * @param callable|array $option
     * @return InputFile
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addHeaderUpload(string $title, string|array $url, callable|array $option = null)
    {
        /** @var InputFile */
        $component = InputFile::make();
        $component->fileField('file');
        $component->receiver($url);
        $component->btnLabel($title);
        // 执行组件设置
        $this->setComponent($component, $option);
        // 添加到头部组件列表
        $this->headerToolbar[] = $component;
        // 返回组件
        return $component;
    }

    /**
     * 添加头部弹窗按钮
     * @param string $title 弹窗标题
     * @param string|array $url 提交API设置
     * @param callable|array $option 回调函数或属性数组
     * @return DialogAction
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function addHeaderDialogBtn(string $title, string|array $url, callable|array $option = null): DialogAction
    {
        /** @var DialogAction */
        $component = DialogAction::make();
        $component->label($title);
        // 执行组件设置
        $this->setComponent($component, $option);
        // 设置弹窗API地址
        $api = $this->router->getAddViewUrl($url, [
            '_dialog' => 1,
        ]);
        // 设置弹窗属性
        $dialog = isset($option['dialog']) && is_array($option['dialog']) ? $option['dialog'] : [];
        $dialogData = [
            'title' => $title,
            'size' => 'md',
            ...$dialog,
            'body' => Service::make()->schemaApi($api),
        ];
        // 设置弹窗标题和内容
        $component->dialog($dialogData);
        // 添加到头部组件列表
        $this->headerToolbar[] = $component;
        // 返回组件
        return $component;
    }

    /**
     * 添加头部单页跳转组件
     * @param string $title 按钮文字
     * @param string $url 跳转地址
     * @param callable|array $option 回调函数或属性数组
     * @return LinkAction
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function addHeaderLinkBtn(string $title, string $url, callable|array $option = null): LinkAction
    {
        /** @var LinkAction */
        $component = LinkAction::make();
        $component->label($title);
        $component->link($url);
        // 执行组件设置
        $this->setComponent($component, $option);
        // 添加到头部组件列表
        $this->headerToolbar[] = $component;
        // 返回组件
        return $component;
    }

    /**
     * 添加头部链接跳转组件
     * @param string $title 按钮文字
     * @param string $url 跳转地址
     * @param callable|array $option 回调函数或属性数组
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function addHeaderUrlBtn(string $title, string $url, callable|array $option = null): UrlAction
    {
        /** @var UrlAction */
        $component = UrlAction::make();
        $component->url($url);
        $component->label($title);
        // 执行组件设置
        $this->setComponent($component, $option);
        // 添加到头部组件列表
        $this->headerToolbar[] = $component;
        // 返回组件
        return $component;
    }

    /**
     * 添加头部确认按钮
     * @param string $title 按钮文字
     * @param string|array $url 获取数据API设置
     * @param callable|array $option 回调函数或属性数组
     * @return AjaxAction
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function addHeaderConfirmBtn(string $title, string|array $url, callable|array $option = null): AjaxAction
    {
        /** @var AjaxAction */
        $component = AjaxAction::make();
        // 设置弹窗API地址
        $api = $this->router->getAddViewUrl($url, [
            '_dialog' => 1,
        ]);
        $component->api($api);
        $component->label($title);
        $component->confirmTitle('温馨提示');
        $component->confirmText('是否确认进行该操作？');
        // 执行组件设置
        $this->setComponent($component, $option);
        // 添加到头部组件列表
        $this->headerToolbar[] = $component;
        // 返回组件
        return $component;
    }
    /**
     * 添加头部抽屉按钮
     * @param string|array $url 获取API设置
     * @param string $title 按钮文字
     * @param callable|array $option 回调函数或属性数组
     * @return DialogAction
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function addHeaderDrawerBtn(string $title, string|array $url, callable|array $option = null): DrawerAction
    {
        /** @var DrawerAction */
        $component = DrawerAction::make();
        $component->label($title);
        // 执行组件设置
        $this->setComponent($component, $option);
        // 设置弹窗API地址
        $api = $this->router->getAddViewUrl($url, [
            '_dialog' => 1,
        ]);
        // 设置抽屉属性
        $component->drawer([
            'title' => $title,
            'body' => Service::make()->schemaApi($api),
        ]);
        // 添加到头部组件列表
        $this->headerToolbar[] = $component;
        // 返回组件
        return $component;
    }

    /**
     * 渲染页面头部工具栏
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function renderHeaderToolbar()
    {
        return $this->headerToolbar;
    }
}
