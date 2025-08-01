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
namespace plugin\xbCode\builder\Renders\crud\layouts;

use plugin\xbCode\builder\Components\Form\Radios;
use plugin\xbCode\builder\Components\Form\Select;
use plugin\xbCode\builder\Components\Form\InputDate;
use plugin\xbCode\builder\Components\Form\InputText;
use plugin\xbCode\builder\Components\Form\Checkboxes;
use plugin\xbCode\builder\Components\Form\InputDateRange;

/**
 * 表单查询布局
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FilterLayout
{
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
     * @return InputText
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterInput(string $field, string $title, mixed $value = ''): InputText
    {
        // 创建组件
        $component = new InputText;
        // 设置组件属性
        $component->name($field);
        $component->label($title);
        $component->value($value);
        $component->placeholder("请填写{$title}");
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
     * @return Select
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterSelect(string $field, string $title, mixed $value = ''): Select
    {
        // 创建组件
        $component = new Select;
        $component->name($field);
        $component->label($title);
        $component->value($value);
        $component->placeholder("请选择{$title}");
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
     * @return InputDate
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterDate(string $field, string $title, mixed $value = ''): InputDate
    {
        // 创建组件
        $component = new InputDate;
        $component->name($field);
        $component->label($title);
        $component->value($value);
        $component->placeholder("请选择{$title}");
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
    public function addFilterDateRange(string $field, string $title, mixed $value = ''): InputDateRange
    {
        // 创建组件
        $component = new InputDateRange;
        $component->name($field);
        $component->label($title);
        $component->value($value);
        $component->placeholder("请选择{$title}");
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
     * @return Checkboxes
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterCheckbox(string $field, string $title, mixed $value = ''): Checkboxes
    {
        // 创建组件
        $component = new Checkboxes;
        $component->name($field);
        $component->label($title);
        $component->value($value);
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
     * @return Radios
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterRadio(string $field, string $title, mixed $value = ''): Radios
    {
        // 创建组件
        $component = new Radios;
        $component->name($field);
        $component->label($title);
        $component->value($value);
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
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addFilterButtons(string $type, string $title, string $level = 'default'): void
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
     * 获取表单查询数据
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getFilter()
    {
        $filter = [
            'title' => $this->filterTitlte,
            'body' => $this->filter,
            'actions' => $this->filterButtons,
        ];
        return count($this->filter) > 0 ? $filter : [];
    }
}
