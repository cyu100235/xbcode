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

/**
 * 积木云选项卡表单渲染器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link http://www.xbcode.net
 * @method $this name(string $value) 字符串注释示例
 */
class XbTabForm extends Base
{
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
     * 选项卡
     * @var Tabs
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected Tabs $tabs;

    /**
     * 设置选项卡字段
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $tabsField = '_tab';

    /**
     * 是否弹窗模式
     * @var bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected bool $isDialog = false;

    /**
     * 选项卡列表数据
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $tabsList = [];

    /**
     * 构造函数
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function __construct()
    {
        // 初始化页面组件
        $this->page = new Page;
        // 初始化选项卡组件
        $this->tabs = new Tabs;
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
        // 初始化表单弹窗状态
        $this->dialog();
    }

    /**
     * 创建选项卡表单
     * @param string $url
     * @return XbTabForm
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
     * 创建选项卡表单
     * @param callable $callback
     * @return XbTabForm
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public static function make(?callable $callback = null)
    {
        $url = static::getCurrentPageUrl();
        $instance = static::instance($url);
        if ($callback) {
            $callback($instance);
        }
        return $instance;
    }

    /**
     * 设置选项卡字段
     * @param string $field
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setTabsField(string $field)
    {
        $this->tabsField = $field;
        return $this;
    }

    /**
     * 获取选项卡字段
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getTabsField()
    {
        return $this->tabsField;
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
     * 添加选项卡
     * @param string $name 选项卡标识符
     * @param string $title 选项卡标题
     * @param array $components 选项卡表单组件数组
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addTab(string $name, string $title, array $components)
    {
        // 添加至选项卡列表
        $this->tabsList[] = [
            'name' => $name,
            'title' => $title,
            'body' => $components,
        ];
        return $this;
    }

    /**
     * 设置为弹窗模式
     * @return static
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
     * 获取选项卡组件
     * @param array $components
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getTabComponents(array $components)
    {
        $components = array_map(function ($item) {
            if (!is_array($item)) {
                $item = $item->getVariables();
            }
            if (!isset($item['type'])) {
                return $item;
            }
            if (isset($item['isSelect'])) {
                unset($item['isSelect']);
            }
            $type = toUnderScore($item['type'] ?? '');
            $type = str_replace('_', '-', $type);
            $item['type'] = $type;
            $item = array_merge_recursive($item, $item['extra'] ?? []);
            if (isset($item['extra'])) {
                unset($item['extra']);
            }
            return $item;
        }, $components);
        return $components;
    }

    /**
     * 获取
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function getCheckUrl(string $name)
    {
        $url = $this->saveApi;
        $url = urldecode($url);
        $path = parse_url($url, PHP_URL_PATH);
        $query = parse_url($url, PHP_URL_QUERY);
        $query = empty($query) ? [] : explode('&', $query);
        if ($this->tabsField) {
            $query = array_merge($query, ["{$this->tabsField}={$name}"]);
        }
        $querys = [];
        foreach ($query as $value) {
            $temp = explode('=', $value);
            $querys[$temp[0]] = $temp[1];
        }
        $query = urldecode(http_build_query($querys));
        $url = "{$path}?{$query}";
        return $url;
    }

    /**
     * 获取选项卡表单
     * @param string $name
     * @param array $components
     * @return AmisForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getFormComponents(string $name, array $components)
    {
        $url = $this->getCheckUrl($name);
        $method = $this->saveMethod;
        $builder = XbForm::make(function (XbForm $builder) use ($components) {
            $builder->useForm()->wrapWithPanel(false);
            foreach ($components as $component) {
                if (isset($component['isSelect'])) {
                    unset($component['isSelect']);
                }
                $builder->addRowRenderComponent($component);
            }
        });
        $builder->setSaveApi($url);
        $builder->setSaveMethod($method);
        $builder->setData($this->data[$name] ?? []);
        $builder->setPrimaryKey($this->primaryKey);
        return $builder->getFormRenderComponent();
    }

    /**
     * 渲染选项卡
     * @return Tabs
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function renderTabs()
    {
        $data = array_map(function ($item) {
            $components = $this->getTabComponents($item['body']);
            $item['body'] = $this->getFormComponents($item['name'], $components);
            unset($item['name']);
            return $item;
        }, $this->tabsList);
        $this->tabs->tabs($data);
        return $this->tabs;
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
            return $this->renderTabs();
        }
        // 页面模式
        $page = $this->page;
        $body = $this->renderTabs();
        $page->body([
            $body,
        ]);
        return $page;
    }
}
