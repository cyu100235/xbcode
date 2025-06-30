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

use plugin\xbCode\builder\Renders\Grid;
use plugin\xbCode\builder\Components\Form\Radios;
use plugin\xbCode\builder\Components\Form\Select;
use plugin\xbCode\builder\Components\Form\InputText;
use plugin\xbCode\builder\Components\Form\InputDate;
use plugin\xbCode\builder\Components\Form\AmisSwitch;
use plugin\xbCode\builder\Components\Form\Checkboxes;
use plugin\xbCode\builder\Components\Form\InputDateRange;

/**
 * 表格筛选查询
 * @copyright 贵州猿创科技有限公司
 * @author 楚羽幽 416716328@qq.com
 */
trait GridFilter
{
    /**
     * 表格实例
     * @var Grid
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    protected Grid $grid;

    /**
     * 筛选查询标题
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected string $filterTitlte = '';

    /**
     * 筛选查询列表
     * @var array
     */
    protected array $filter = [];

    /**
     * 筛选查询按钮
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $filterButtons = [
        [
            'type' => 'submit',
            'label' => '查询',
            'level' => 'primary',
        ],
        [
            'type' => 'reset',
            'label' => '重置',
            'level' => 'default',
        ],
    ];

    /**
     * 创建输入框查询
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterInput(string $field, string $title, mixed $value = '', callable|array $option = null): InputText
    {
        // 创建组件
        $component = new InputText;
        // 设置组件属性
        $component->name($field);
        $component->label($title);
        $component->value($value);
        $component->placeholder("请填写{$title}");
        // 动态设置组件属性
        $this->setComponent($component, $option);
        // 将组件添加到筛选查询列表
        $this->filter[] = $component;
        // 返回组件实例
        return $component;
    }

    /**
     * 创建下拉选择查询
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return Select
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterSelect(string $field, string $title, mixed $value = '', callable|array $option = null): Select
    {
        // 创建组件
        $component = new Select;
        $component->name($field);
        $component->label($title);
        $component->value($value);
        $component->placeholder("请选择{$title}");
        // 动态设置组件属性
        $this->setComponent($component, $option);
        // 将组件添加到筛选查询列表
        $this->filter[] = $component;
        // 返回组件实例
        return $component;
    }

    /**
     * 创建日期选择查询
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return InputDate
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterDate(string $field, string $title, mixed $value = '', callable|array $option = null): InputDate
    {
        // 创建组件
        $component = new InputDate;
        $component->name($field);
        $component->label($title);
        $component->value($value);
        $component->placeholder("请选择{$title}");
        // 动态设置组件属性
        $this->setComponent($component, $option);
        // 将组件添加到筛选查询列表
        $this->filter[] = $component;
        // 返回组件实例
        return $component;
    }

    /**
     * 创建日期范围查询
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return InputDateRange
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterDateRange(string $field, string $title, mixed $value = '', callable|array $option = null): InputDateRange
    {
        // 创建组件
        $component = new InputDateRange;
        $component->name($field);
        $component->label($title);
        $component->value($value);
        $component->placeholder("请选择{$title}");
        // 动态设置组件属性
        $this->setComponent($component, $option);
        // 将组件添加到筛选查询列表
        $this->filter[] = $component;
        // 返回组件实例
        return $component;
    }

    /**
     * 创建复选框查询
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return Checkboxes
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterCheckbox(string $field, string $title, mixed $value = '', callable|array $option = null): Checkboxes
    {
        // 创建组件
        $component = new Checkboxes;
        $component->name($field);
        $component->label($title);
        $component->value($value);
        // 动态设置组件属性
        $this->setComponent($component, $option);
        // 将组件添加到筛选查询列表
        $this->filter[] = $component;
        // 返回组件实例
        return $component;
    }

    /**
     * 创建单选框查询
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return Radios
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterRadio(string $field, string $title, mixed $value = '', callable|array $option = null): Radios
    {
        // 创建组件
        $component = new Radios;
        $component->name($field);
        $component->label($title);
        $component->value($value);
        // 动态设置组件属性
        $this->setComponent($component, $option);
        // 将组件添加到筛选查询列表
        $this->filter[] = $component;
        // 返回组件实例
        return $component;
    }

    /**
     * 创建开关查询
     * @param string $field
     * @param string $title
     * @param mixed $value
     * @param callable|array $option
     * @return AmisSwitch
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterSwitch(string $field, string $title, mixed $value = '', callable|array $option = null): AmisSwitch
    {
        // 创建组件
        $component = new AmisSwitch;
        $component->name($field);
        $component->label($title);
        $component->value($value);
        // 动态设置组件属性
        $this->setComponent($component, $option);
        // 将组件添加到筛选查询列表
        $this->filter[] = $component;
        // 返回组件实例
        return $component;
    }

    /**
     * 设置筛选查询标题
     * @param string $title 标题内容
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function setFilterTitle(string $title): void
    {
        $this->filterTitlte = $title;
    }

    /**
     * 设置筛选查询按钮
     * @param string $type 按钮类型，如：submit, reset, button等
     * @param string $title 按钮标题
     * @param string $level 按钮样式级别，如：primary, default, danger等
     * @param array $option 其他选项参数
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterButtons(string $type, string $title, string $level = 'default', array $option = []): void
    {
        // 检查按钮类型是否已添加
        if (isset($this->filterButtons[$type])) {
            return;
        }
        foreach($this->filterButtons as $item){
            if(isset($item['type'])){
                return;
            }
            if($item['type'] === $type){
                return;
            }
        }
        $this->filterButtons[] = [
            'type' => $type,
            'label' => $title,
            'level' => $level,
        ];
    }

    /**
     * 渲染筛选查询
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function renderCURDFilter()
    {
        $filter = [
            'title' => $this->filterTitlte,
            'body' => $this->filter,
            'actions' => $this->filterButtons,
        ];
        return count($this->filter) > 0 ? $filter : [];
    }
}
