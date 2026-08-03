<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders;

use plugin\xbCode\builder\Components\Page;
use plugin\xbCode\builder\Components\Tabs;
use plugin\xbCode\builder\Renders\form\FormBase;
use plugin\xbCode\builder\Renders\form\FormData;
use plugin\xbCode\builder\Components\Form\AmisForm;
use plugin\xbCode\builder\Renders\form\layouts\FormLayout;
use plugin\xbCode\builder\Renders\form\layouts\ToolbarLayout;

/**
 * 积木云表单渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this name(string $value) 字符串注释示例
 */
class XbForm extends Base
{
    // 工具栏布局
    use ToolbarLayout;
    // 表单布局
    use FormLayout;
    // 表单基础能力
    use FormBase;
    // 表单数据能力
    use FormData;

    /**
     * 页面组件
     * @var Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Page $page;

    /**
     * 表单组件
     * @var AmisForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected AmisForm $form;

    /**
     * 选项卡
     * @var Tabs
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Tabs $tabs;

    /**
     * 是否弹窗模式
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $isDialog = false;

    /**
     * 构造函数
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct()
    {
        // 初始化页面组件
        $this->page = new Page;
        // 初始化表单组件
        $this->form = new AmisForm;
        // 初始化选项卡组件
        $this->tabs = new Tabs;
    }

    /**
     * 初始化组件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function init()
    {
        // 设置初始化接口
        $this->useForm()->initApi([
            'api' => $this->url,
            'method' => $this->method,
        ]);
        // 初始化表单弹窗状态
        $this->dialog();
    }

    /**
     * 组件实例
     * @param string $url 当前页面地址
     * @return XbForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function instance(string $url)
    {
        $component = new static;
        $component->setUrl($url);
        $component->setSaveApi($url);
        $component->setSaveMethod('POST');
        $component->init();
        return $component;
    }

    /**
     * 创建表单
     * @param callable $callback 组件参数
     * @return XbForm
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public static function make(?callable $callback = null)
    {
        $url = static::getCurrentPageUrl();
        $component = static::instance($url);
        if ($callback) {
            $callback($component);
        }
        return $component;
    }

    /**
     * 渲染表单组件
     * @param array $components
     * @return XbForm
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public static function render(array $components)
    {
        $url = static::getCurrentPageUrl();
        $instance = static::instance($url);
        $instance->addRowRenderComponents($components);
        return $instance;
    }

    /**
     * 获取表单组件实例
     * @return AmisForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useForm()
    {
        return $this->form;
    }

    /**
     * 获取选项卡实例
     * @return Tabs
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useTabs()
    {
        return $this->tabs;
    }

    /**
     * 设置弹窗模式状态
     * @return XbForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function dialog()
    {
        $dialog = false;
        if (str_contains($this->url, '_replace')) {
            $dialog = true;
        }
        $this->isDialog = $dialog;
        return $this;
    }

    /**
     * 获取表单渲染器
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function create(): mixed
    {
        // 弹窗模式
        if ($this->isDialog) {
            // 获取组件内容
            $component = $this->renderForm();
            // 弹窗模式-取消边框
            $component->wrapWithPanel(false);
            // 提交成功时刷新表格
            $component->reload('crud');
            // 提交成功时关闭弹窗
            $component->close(true);
            // 返回组件
            return $component;
        }
        // 页面模式
        $page = $this->page;
        // 设置表单工具栏
        $toolbar = $this->renderHeaderToolbar();
        if ($toolbar) {
            $page->toolbar($toolbar);
        }
        // 获取渲染表单
        $form = $this->renderForm();
        if ($this->redirect) {
            $form->redirect($this->redirect);
        }
        $page->body([
            $form,
        ]);
        // 返回页面实例
        return $page;
    }
}
