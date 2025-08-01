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
namespace plugin\xbCode\builder\Renders;

use plugin\xbCode\builder\Components\Page;
use plugin\xbCode\builder\Renders\form\FormBase;
use plugin\xbCode\builder\Renders\form\FormData;
use plugin\xbCode\builder\Components\Form\AmisForm;
use plugin\xbCode\builder\Renders\form\layouts\FormLayout;
use plugin\xbCode\builder\Renders\form\layouts\ToolbarLayout;

/**
 * 表单渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this name(string $value) 字符串注释示例
 */
class Form extends Base
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
     * 创建表单渲染器
     * @param callable $func
     * @param string $url
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make(callable $func, string $url)
    {
        $component = new static;
        $component->setUrl($url);
        $component->init();
        $func($component);
        return $component;
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
     * 设置弹窗模式状态
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function dialog()
    {
        $dialog = false;
        if(str_contains($this->url, '_dialog')) {
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
    public function create():mixed
    {
        // 弹窗模式
        if ($this->isDialog) {
            return $this->renderForm();
        }
        // 页面模式
        $page = $this->page;
        $toolbar = $this->renderHeaderToolbar();
        if ($toolbar) {
            $page->toolbar($toolbar);
        }
        $page->body([
            $this->renderForm(),
        ]);
        // 返回页面实例
        return $page;
    }
}
