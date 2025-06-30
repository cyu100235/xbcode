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
use plugin\xbCode\builder\Components\Action\AjaxAction;
use plugin\xbCode\builder\Components\Action\LinkAction;
use plugin\xbCode\builder\Components\Action\DialogAction;
use plugin\xbCode\builder\Components\Action\DrawerAction;
use plugin\xbCode\builder\Renders\Common\Router;

/**
 * 表格操作按钮
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait GridActionButtons
{
    /**
     * 路由组件
     * @var Router
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Router $router;

    /**
     * 操作选项配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $config = [
        'label' => '操作',
        'width' => 'auto',
    ];

    /**
     * 表格右侧操作按钮
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $rightActionButtons = [];

    /**
     * 获取操作选项配置
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getCRUDConfig(): array
    {
        return $this->config;
    }

    /**
     * 设置操作选项配置
     * @param string $name
     * @param mixed $value
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setCRUDActionConfig(string $name, mixed $value): self
    {
        $this->config[$name] = $value;
        return $this;
    }

    /**
     * 添加操作弹窗按钮
     * @param string|array $title 弹窗标题
     * @param string|array $url 获取数据接口配置
     * @param callable|array $option 回调函数或属性数组
     * @return DialogAction
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function addActionDialogBtn(string $title,string|array $url, callable|array $option = null): DialogAction
    {
        /** @var DialogAction */
        $component = DialogAction::make();
        $component->label($title);
        // 执行组件设置
        $this->setComponent($component, $option);
        // 提交接口配置
        $api = $this->router->getEditViewUrl($url,[
            '_dialog' => 1,
        ]);
        // 设置弹窗属性
        $dialog = isset($option['dialog']) && is_array($option['dialog']) ? $option['dialog'] : [];
        // 设置弹窗标题和内容
        $component->dialog([
            'title' => $title,
            'size' => 'lg',
            ...$dialog,
            'body' => Service::make()->schemaApi($api),
        ]);
        // 添加到组件列表
        $this->rightActionButtons[] = $component;
        // 返回组件
        return $component;
    }

    /**
     * 添加操作单页跳转组件
     * @param string $title 按钮文字
     * @param string $url 跳转地址
     * @param callable|array $option 回调函数或属性数组
     * @return LinkAction
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function addActionLinkBtn(string $title,string $url, callable|array $option = null): LinkAction
    {
        /** @var LinkAction */
        $component = LinkAction::make();
        $component->label($title);
        $component->link($url);
        // 执行组件设置
        $this->setComponent($component, $option);
        // 添加到组件列表
        $this->rightActionButtons[] = $component;
        // 返回组件
        return $component;
    }

    /**
     * 添加操作链接跳转组件
     * @param string $title 按钮文字
     * @param string $url 跳转地址
     * @param callable|array $option 回调函数或属性数组
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function addActionUrlBtn(string $title,string $url, callable|array $option = null): UrlAction
    {
        /** @var UrlAction */
        $component = UrlAction::make();
        $component->url($url);
        $component->label($title);
        // 执行组件设置
        $this->setComponent($component, $option);
        // 添加到组件列表
        $this->rightActionButtons[] = $component;
        // 返回组件
        return $component;
    }

    /**
     * 添加操作确认按钮
     * @param string $title 按钮文字
     * @param string|array $url 获取数据接口配置
     * @param callable|array $option 回调函数或属性数组
     * @return AjaxAction
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function addActionConfirmBtn(string $title,string|array $url, callable|array $option = null): AjaxAction
    {
        /** @var AjaxAction */
        $component = AjaxAction::make();
        // 提交API配置
        $api = $this->router->getEditViewUrl($url,[
            '_dialog' => 1,
        ]);
        $component->api($api);
        $component->label($title);
        $component->confirmTitle('温馨提示');
        $component->confirmText('是否确认进行该操作？');
        // 执行组件设置
        $this->setComponent($component, $option);
        // 添加到组件列表
        $this->rightActionButtons[] = $component;
        // 返回组件
        return $component;
    }
    /**
     * 添加操作抽屉按钮
     * @param string $title 按钮文字
     * @param string|array $url 获取数据接口配置
     * @param callable|array $option 回调函数或属性数组
     * @return DialogAction
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function addActionDrawerBtn(string $title,string|array $url, callable|array $option = null): DrawerAction
    {
        /** @var DrawerAction */
        $component = DrawerAction::make();
        $component->label($title);
        // 执行组件设置
        $this->setComponent($component, $option);
        // 提交数据接口配置
        $api = $this->router->getEditViewUrl($url,[
            '_dialog' => 1,
        ]);
        // 设置抽屉属性
        $component->drawer([
            'title' => $title,
            'body' => Service::make()->schemaApi($api),
        ]);
        // 添加到组件列表
        $this->rightActionButtons[] = $component;
        // 返回组件
        return $component;
    }

    /**
     * 渲染CURD操作按钮
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function renderCURDActionButtons(): array
    {
        return $this->rightActionButtons;
    }
}
