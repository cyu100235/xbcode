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
namespace plugin\xbCode\builder\Renders\Form;

use plugin\xbCode\builder\Components\Form\AmisForm;
use plugin\xbCode\builder\Components\Form\FormBase;
use plugin\xbCode\builder\Renders\Form\rows\FormRowDate;
use plugin\xbCode\builder\Renders\Form\rows\FormRowFlex;
use plugin\xbCode\builder\Renders\Form\rows\FormRowGroup;
use plugin\xbCode\builder\Renders\Form\rows\FormRowInput;
use plugin\xbCode\builder\Renders\Form\rows\FormRowClick;
use plugin\xbCode\builder\Renders\Form\rows\FormRowUpload;
use plugin\xbCode\builder\Renders\Form\rows\FormRowDisplay;
use plugin\xbCode\builder\Renders\Form\rows\FormRowOptions;

/**
 * 表单视图渲染
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FormView
{
    use FormRowDate;
    use FormRowInput;
    use FormRowClick;
    use FormRowUpload;
    use FormRowOptions;
    use FormRowDisplay;
    use FormRowGroup;
    use FormRowFlex;

    /**
     * 表单实例
     * @var AmisForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected AmisForm $form;

    protected array $formView = [];

    /**
     * 表单按钮
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $customSubmit = [
        [
            'type' => 'submit',
            'level' => 'primary',
            'label' => '提交保存',
        ],
    ];

    /**
     * 初始表单设置
     * @param string $api
     * @param string $title
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function formConfig(string $title = '数据编辑')
    {
        $this->form->title($title);
        $this->form->mode('normal');
        $this->form->wrapWithPanel(false);
    }

    /**
     * 添加表单组件
     * @param string $type 组件名称或组件类
     * @param string $field 组件字段
     * @param string $title 组件标题
     * @param mixed $value 组件默认值
     * @param callable|array $option 
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRow(string $type, string $field, string $title, mixed $value = '', callable|array $option = [])
    {
        // 检查非命名空间的组件名称
        if (!str_contains($type, '\\')) {
            // 首字母转大写
            $type = ucfirst($type);
            // 如果是组件名称，则转换为类名
            $displayComponent = 'plugin\\xbCode\\builder\\Components\\' . $type;
            $formComponent = 'plugin\\xbCode\\builder\\Components\\Form\\' . $type;
            if(class_exists($displayComponent)) {
                $type = $displayComponent;
            }else if(class_exists($formComponent)) {
                $type = $formComponent;
            }
        }
        /** @var FormBase */
        $component = new $type;
        $component->name($field);
        $component->label($title);
        $component->value($value);

        // 设置组件提示占位符
        $componentType = $component->getComponentType();
        if (!$componentType) {
            $component->placeholder("请填写{$title}");
        }
        $this->setComponent($component, $option);
        $this->formView[] = $component;
        return $component;
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
     * 获取自定义提交按钮
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getCustomSubmit(): array
    {
        return $this->customSubmit;
    }

    /**
     * 渲染表单
     * @return AmisForm
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function renderForm(): AmisForm
    {
        // 设置表单布局模式
        if ($this->customLayout) {
            $items = $this->customLayout;
        } else {
            $items = $this->formView;
        }

        // 弹窗表单设置
        if ($this->isDialog()) {
            // 弹窗模式-取消面板包裹
            $this->form->wrapWithPanel(false);
        } else if ($this->customSubmit) {
            // 普通模式-自定义提交按钮
            $actions = $this->customSubmit;
            $items = array_merge($items, $actions);
        }

        // 设置表单内容
        $this->form->body($items);
        // 返回表单实例
        return $this->form;
    }
}
