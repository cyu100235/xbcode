<?php
namespace plugin\xbCode\builder\Renders\Grid\Column;

use Closure;
use plugin\xbCode\builder\Components\BaseSchema;
use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 表格列组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/table
 * @method $this type(string $value) 表格列类型
 * @method $this fixed(string $value) 配置是否固定当前列 'left' | 'right' | 'none'
 * @method $this popOver(string $value) 配置查看详情功能
 * @method $this quickEdit(array|bool $value) 配置快速编辑功能
 * @method $this quickEditOnUpdate(mixed $value) 作为表单项时，可以单独配置编辑时的快速编辑面板
 * @method $this copyable(string $value) 配置点击复制功能
 * @method $this sortable(string $value) 配置是否可以排序
 * @method $this searchable(string $value) 是否可快速搜索
 * @method $this toggled(string $value) 配置是否默认展示
 * @method $this width(string $value) 列宽度
 * @method $this align(string $value) 列对齐方式 'left' | 'right' | 'center' | 'justify'
 * @method $this className(string $value) 列样式表
 * @method $this classNameExpr(string $value) 单元格样式表达式
 * @method $this labelClassName(string $value) 列头样式表
 * @method $this filterable(string $value) todo
 * @method $this breakpoint(string $value) 结合表格 footable 使用。填写 *、xs、sm、md、lg指定 footable 的触发条件，填写多个用空格隔开
 * @method $this remark(string $value) 提示信息
 * @method $this value(string $value) 默认值, 只有在 inputTable 里面才有用
 * @method $this unique(string $value) 是否唯一, 只有在 inputTable 里面才有用
 * @method getValue(string $value) 给组件赋值时自定义处理
 * @method setValue(string $value) 组件赋值提交时自定义处理
 * @method defaultAttr() 可以自定义属性的设置
 */
class Column
{
    use ColumnEdit;
    use ColumnDisplay;

    protected string $label;
    protected string $name;

    protected BaseSchema $tableColumn;

    /**
     * 构造函数
     * @param string $name
     * @param string $label
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function __construct(string $name, string $label)
    {
        if (empty($label)) {
            $label = $name;
        }
        $this->label = $label;
        $this->name = $name;
        $this->tableColumn = TableColumn::make()->name($name)->label($label);
    }

    /**
     * 创建组件
     * @param mixed $name
     * @param mixed $label
     * @return static
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public static function make($name, $label)
    {
        return new static($name, $label);
    }


    /**
     * 设置组件属性
     * @param string $name
     * @param mixed $arguments
     * @return static
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 416716328@qq.com
     */
    public function __call(string $name, mixed $arguments)
    {
        $this->tableColumn->$name(...$arguments);
        return $this;
    }


    /**
     * 自定义组件
     * @param $typeComponent
     * @return TableColumn
     */
    public function useTableColumn($typeComponent = null)
    {
        if ($typeComponent) {
            if ($typeComponent instanceof Closure) {
                $typeComponent = $typeComponent();
            }

            foreach ($this->tableColumn as $key => $value) {
                if (!property_exists($typeComponent, $key)) {
                    $typeComponent->$key = $value;
                }
            }
            $this->tableColumn = $typeComponent;
        }
        return $this->tableColumn;
    }


    /**
     * 获取name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * @return BaseSchema|TableColumn
     */
    public function render()
    {
        return $this->tableColumn;
    }
}
