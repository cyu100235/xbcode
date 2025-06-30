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

use Closure;
use JsonSerializable;
use plugin\xbCode\builder\Components\Page;
use plugin\xbCode\builder\Renders\Common\Router;
use plugin\xbCode\builder\Renders\Form\FormView;
use plugin\xbCode\builder\Components\Form\AmisForm;
use plugin\xbCode\builder\Renders\Form\base\FormData;
use plugin\xbCode\builder\Renders\Common\HeaderToolbar;

/**
 * 表单渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this name(string $value) 字符串注释示例
 */
class Form implements JsonSerializable
{
    // 页面工具栏实现
    use HeaderToolbar;
    // 表单组件实现
    use FormView;
    // 表单数据处理
    use FormData;

    /**
     * 页面实例
     * @var Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Page $page;

    /**
     * 路由组件
     * @var Router
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Router $router;

    /**
     * 表单实例
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
     * 自定义布局
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $customLayout = [];

    /**
     * 构造函数
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct()
    {
        $this->page = Page::make();
        $this->form = AmisForm::make();
        $this->router = Router::make();
    }

    /**
     * 创建表单实例
     * @param callable $func 表单配置回调函数
     * @param string $saveApi 保存接口地址
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make(callable $func, string $saveApi): Form
    {
        $form = new static();
        $form->setApi($saveApi . '?id=${id}');
        $func($form);
        return $form;
    }

    /**
     * 获取页面实例
     * @return Page
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function usePage(): Page
    {
        return $this->page;
    }

    /**
     * 获取路由组件
     * @return Router
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useRouter(): Router
    {
        return $this->router;
    }

    /**
     * 获取表单实例
     * @return AmisForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function useForm(): AmisForm
    {
        return $this->form;
    }

    /**
     * 设置表单配置
     * @param string $name
     * @param mixed $value
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setConfig(string $name, mixed $value)
    {
        $this->form->setVariable($name, $value);
    }

    /**
     * 获取是否弹窗模式
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function isDialog(): bool
    {
        return $this->isDialog;
    }

    /**
     * 设置是否弹窗模式
     * @param bool $dialog
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function dialog(bool $dialog = true): Form
    {
        $this->isDialog = $dialog;
        return $this;
    }

    /**
     * 渲染表单
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function jsonSerialize(): mixed
    {
        // 弹窗模式
        if ($this->isDialog()) {
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
