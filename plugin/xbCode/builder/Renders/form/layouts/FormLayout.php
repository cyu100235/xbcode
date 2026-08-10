<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\layouts;

use plugin\xbCode\builder\Renders\form\rows\TplRow;
use plugin\xbCode\builder\Components\Form\AmisForm;
use plugin\xbCode\builder\Components\Form\FormBase;
use plugin\xbCode\builder\Renders\form\rows\GridRow;
use plugin\xbCode\builder\Renders\form\rows\AmapRow;
use plugin\xbCode\builder\Renders\form\rows\BmapRow;
use plugin\xbCode\builder\Renders\form\rows\FlexRow;
use plugin\xbCode\builder\Renders\form\rows\IconRow;
use plugin\xbCode\builder\Renders\form\rows\TabsRow;
use plugin\xbCode\builder\Renders\form\rows\TimeRow;
use plugin\xbCode\builder\Renders\form\rows\HtmlRow;
use plugin\xbCode\builder\Renders\form\rows\CityRow;
use plugin\xbCode\builder\Renders\form\rows\DateRow;
use plugin\xbCode\builder\Renders\form\rows\VideoRow;
use plugin\xbCode\builder\Renders\form\rows\AudioRow;
use plugin\xbCode\builder\Renders\form\rows\ComboRow;
use plugin\xbCode\builder\Renders\form\rows\GroupRow;
use plugin\xbCode\builder\Renders\form\rows\ArrayRow;
use plugin\xbCode\builder\Renders\form\rows\ColorRow;
use plugin\xbCode\builder\Renders\form\rows\ImageRow;
use plugin\xbCode\builder\Renders\form\rows\InputRow;
use plugin\xbCode\builder\Renders\form\rows\RadioRow;
use plugin\xbCode\builder\Renders\form\rows\AlertRow;
use plugin\xbCode\builder\Renders\form\rows\SelectRow;
use plugin\xbCode\builder\Renders\form\rows\StaticRow;
use plugin\xbCode\builder\Renders\form\rows\SwitchRow;
use plugin\xbCode\builder\Renders\form\rows\VditorRow;
use plugin\xbCode\builder\Renders\form\rows\HiddenRow;
use plugin\xbCode\builder\Renders\form\rows\DividerRow;
use plugin\xbCode\builder\Renders\form\rows\InputKvsRow;
use plugin\xbCode\builder\Renders\form\rows\MarkdownRow;
use plugin\xbCode\builder\Renders\form\rows\CodeEditRow;
use plugin\xbCode\builder\Renders\form\rows\DateTimeRow;
use plugin\xbCode\builder\Renders\form\rows\CheckBoxRow;
use plugin\xbCode\builder\Renders\form\rows\TagInputRow;
use plugin\xbCode\builder\Renders\form\rows\TransferRow;
use plugin\xbCode\builder\Renders\form\rows\UrlInputRow;
use plugin\xbCode\builder\Renders\form\rows\FieldSetRow;
use plugin\xbCode\builder\Renders\form\rows\TextareaRow;
use plugin\xbCode\builder\Renders\form\rows\ComponentRow;
use plugin\xbCode\builder\Renders\form\rows\DateRangeRow;
use plugin\xbCode\builder\Renders\form\rows\InputTreeRow;
use plugin\xbCode\builder\Renders\form\rows\InputRangeRow;
use plugin\xbCode\builder\Renders\form\rows\TreeSelectRow;
use plugin\xbCode\builder\Renders\form\rows\ListSelectRow;
use plugin\xbCode\builder\Renders\form\rows\InputGroupRow;
use plugin\xbCode\builder\Renders\form\rows\InputTableRow;
use plugin\xbCode\builder\Renders\form\rows\IconPickerRow;
use plugin\xbCode\builder\Renders\form\rows\UploadFileRow;
use plugin\xbCode\builder\Renders\form\rows\EmailInputRow;
use plugin\xbCode\builder\Renders\form\rows\InputArrayRow;
use plugin\xbCode\builder\Renders\form\rows\WangEditorRow;
use plugin\xbCode\builder\Renders\form\rows\UploadAudioRow;
use plugin\xbCode\builder\Renders\form\rows\UploadImageRow;
use plugin\xbCode\builder\Renders\form\rows\UploadVideoRow;
use plugin\xbCode\builder\Renders\form\rows\NumberInputRow;
use plugin\xbCode\builder\Renders\form\rows\InputRatingRow;
use plugin\xbCode\builder\Renders\form\rows\ActionButtonRow;
use plugin\xbCode\builder\Renders\form\rows\NormalEditorRow;
use plugin\xbCode\builder\Renders\form\rows\PasswordInputRow;
use plugin\xbCode\builder\Renders\form\rows\DateTimeRangeRow;
use plugin\xbCode\builder\Renders\form\rows\NestedSelecttRow;
use plugin\xbCode\builder\Renders\form\rows\ChainedSelectRow;
use plugin\xbCode\builder\Renders\form\rows\InputKeyValueRow;
use plugin\xbCode\builder\Renders\form\rows\UploadAttachmentRow;
use plugin\xbCode\builder\Renders\form\rows\MatrixCheckboxesRow;
use plugin\xbCode\builder\Renders\form\rows\ButtonGroupSelectRow;
use think\helper\Str;

/**
 * 表单布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormLayout
{
    // 实现表单项
    use TplRow;
    use GridRow;
    use TabsRow;
    use IconRow;
    use HtmlRow;
    use DateRow;
    use CityRow;
    use TimeRow;
    use FlexRow;
    use AmapRow;
    use BmapRow;
    use AlertRow;
    use VideoRow;
    use AudioRow;
    use ComboRow;
    use ImageRow;
    use ColorRow;
    use ColorRow;
    use InputRow;
    use RadioRow;
    use GroupRow;
    use ArrayRow;
    use HiddenRow;
    use StaticRow;
    use SelectRow;
    use SwitchRow;
    use VditorRow;
    use DividerRow;
    use TransferRow;
    use DateTimeRow;
    use CodeEditRow;
    use CheckBoxRow;
    use UrlInputRow;
    use TagInputRow;
    use FieldSetRow;
    use TextareaRow;
    use MarkdownRow;
    use InputKvsRow;
    use DateRangeRow;
    use InputTreeRow;
    use ComponentRow;
    use ListSelectRow;
    use EmailInputRow;
    use IconPickerRow;
    use InputGroupRow;
    use InputTableRow;
    use UploadFileRow;
    use TreeSelectRow;
    use InputArrayRow;
    use InputRangeRow;
    use WangEditorRow;
    use UploadImageRow;
    use UploadAudioRow;
    use UploadVideoRow;
    use NumberInputRow;
    use InputRatingRow;
    use ActionButtonRow;
    use NormalEditorRow;
    use InputKeyValueRow;
    use PasswordInputRow;
    use DateTimeRangeRow;
    use NestedSelecttRow;
    use ChainedSelectRow;
    use UploadAttachmentRow;
    use MatrixCheckboxesRow;
    use ButtonGroupSelectRow;

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
     * 表单行组件
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $formRows = [];

    /**
     * 表单按钮
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $customSubmit = [
        [
            'type' => 'submit',
            'actionType' => 'submit',
            'level' => 'primary',
            'label' => '提交保存',
        ],
    ];

    /**
     * 获取表单组件实例
     * @param string $type
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @throws \Exception
     * @return FormBase
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getRowComponent(string $type, string $field, string $title, mixed $value = '', callable|array $option = [])
    {
        if (is_string($type) && str_contains($type, '-')) {
            $type = Str::studly($type);
        }
        if (!class_exists($type) && !str_contains($type, '\\')) {
            // 首字母转大写
            $type = ucfirst($type);
            // 命名空间
            $namespace = "plugin\\xbCode\\builder\\Components\\";
            // 如果是组件名称，则转换为类名
            $component = "{$namespace}{$type}";
            if (!class_exists($component)) {
                $component = "{$namespace}Form\\{$type}";
            }
            if (!class_exists($component)) {
                $component = "{$namespace}Custom\\{$type}";
            }
        } else {
            $component = $type;
        }
        if (!class_exists($component)) {
            throw new \Exception("组件类 {$component} 不存在");
        }
        /** @var FormBase */
        $component = new $component;
        if ($field) {
            $component->name($field);
        }
        if ($title) {
            $component->label($title);
        }
        $component->value($value);

        // 设置组件提示占位符
        $componentType = null;
        if (method_exists($component, 'getComponentType')) {
            $componentType = $component->getComponentType();
        }
        // 设置组件提示占位符
        $placeholder = $componentType ? "请选择{$title}" : "请填写{$title}";
        // 设置占位符
        $component->placeholder($placeholder);
        if (is_array($option)) {
            // 如果是数组配置，则直接设置变量
            $component->setVariables($option);
        } else if (is_callable($option)) {
            // 如果是回调函数，则执行回调
            $option($component);
        }
        return $component;
    }

    /**
     * 获取组件列表名称
     * @param array $components
     * @param array $names
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getComponentsName(array $components, array $names = [])
    {
        foreach ($components as $component) {
            // 兼容数组和对象方式
            if (is_array($component)) {
                if (isset($component['name'])) {
                    $names[] = $component['name'];
                }
                if (isset($component['groupName'])) {
                    $names[] = $component['groupName'];
                }
                if (isset($component['items'])) {
                    $temp = $this->getComponentsName($component['items'], $names);
                    $names = array_merge($names, $temp);
                }
                if (isset($component['body'])) {
                    $temp = $this->getComponentsName($component['body'], $names);
                    $names = array_merge($names, $temp);
                }
            } else if (is_object($component)) {
                if (isset($component->name)) {
                    $names[] = $component->name;
                }
                if (isset($component->groupName)) {
                    $names[] = $component->groupName;
                }
                if (isset($component->items)) {
                    $temp = $this->getComponentsName($component->items, $names);
                    $names = array_merge($names, $temp);
                }
                if (isset($component->body)) {
                    $temp = $this->getComponentsName($component->body, $names);
                    $names = array_merge($names, $temp);
                }
            }
        }
        $names = array_unique($names);
        return $names;
    }

    /**
     * 排除重复的表单项组件
     * @param array $components
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function excludeFormRows(array $components)
    {
        $names = $this->getComponentsName($components);
        $formRows = $this->formRows;
        foreach ($formRows as $key => $value) {
            // 兼容数组和对象方式
            if (is_array($value)) {
                $name = $value['name'] ?? '';
                $groupName = $value['groupName'] ?? '';
            } else if (is_object($value)) {
                $name = $value->name ?? '';
                $groupName = $value->groupName ?? '';
            } else {
                continue;
            }
            // 排除重复的表单项
            if (in_array($name, $names)) {
                unset($formRows[$key]);
            }
            // 排除重复的分组表单
            if ($groupName && in_array($groupName, $names)) {
                unset($formRows[$key]);
            }
        }
        $formRows = array_values($formRows);
        $this->formRows = $formRows;
        return $formRows;
    }

    /**
     * 添加表单项组件
     * @param string $type
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @throws \Exception
     * @return FormBase
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRow(string $type, string $field, string $title, mixed $value = '', callable|array $option = [])
    {
        $component = $this->getRowComponent($type, $field, $title, $value, $option);
        $this->formRows[] = $component;
        return $component;
    }

    /**
     * 批量添加成品组件
     * @param array $components 组件列表
     * @author 楚羽幽 958416459@qq.com
     * @copyright 贵州积木云网络科技有限公司
     */
    public function addRowRenderComponents(array $components)
    {
        foreach ($components as $component) {
            $this->addRowRenderComponent($component);
        }
        return $this;
    }

    /**
     * 添加成品组件
     * @param array|object $component 组件实例
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowRenderComponent(array|object $component)
    {
        if (is_object($component)) {
            $component = $component->get();
        }
        if (!isset($component['type'])) {
            throw new \Exception("组件类型不存在");
        }
        $this->formRows[] = $component;
        return $this;
    }

    /**
     * 设置自定义提交按钮
     * @param array $option
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFormButton(array $option = [])
    {
        // 设置自定义提交按钮
        $this->customSubmit = $option;
        // 返回当前实例
        return $this;
    }

    /**
     * 渲染表单
     * @return AmisForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function renderForm()
    {
        // 获取表单组件
        $component = $this->form;

        // 设置提交请求接口
        $component->api([
            'url' => $this->saveApi,
            'method' => $this->saveMethod,
        ]);

        // 获取表单组件
        $formRows = $this->formRows;

        // 是否有选项卡
        $tabs = $this->tabs->tabs;
        if (!empty($tabs)) {
            // 获取选项卡组件
            $tabComponents = $this->renderTabs();
            // 设置组件为选项卡
            $formRows = [$tabComponents];
            // 设置表单样式
            $component->className('xb-tabs-form');
        }
        // 是否有自定义按钮
        if (!empty($this->customSubmit) && !$this->isDialog && empty($tabs)) {
            $formRows = array_merge($formRows, $this->customSubmit);
        }
        // 设置表单组件
        $component->body($formRows);
        // 设置表单数据
        $component->data($this->data ?: null);
        // 返回表单实例
        return $component;
    }

    /**
     * 获取表单渲染后组件
     * @return AmisForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getFormRenderComponent()
    {
        return $this->renderForm();
    }
}