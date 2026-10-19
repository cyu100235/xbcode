<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\crud;

use plugin\xbCode\builder\Components\Table\TableColumn;

/**
 * 表格单元列
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait ColumnUtil
{
    /**
     * 表格列配置
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $columns = [];

    /**
     * 使用自定义组件列
     * @param mixed $component
     * @param string $name
     * @param string $title
     * @return TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function useCustomColumn(mixed $component, string $name, string $title, callable|array $option = [])
    {
        $component = $this->buildColumn($component, $name, $title, $option);
        $this->columns[] = $component;
        return $component;
    }

    /**
     * 构建表格列组件（不追加到列集合）
     * @param mixed $component 组件类名或组件实例
     * @param string $name 列键名
     * @param string $title 列标题
     * @param callable|array $option 列配置
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function buildColumn(mixed $component, string $name, string $title, callable|array $option = [])
    {
        if (!is_object($component)) {
            /** @var TableColumn */
            $component = new $component;
        }
        $component->name($name);
        $component->label($title);
        if (is_array($option)) {
            // 如果是数组，则设置变量
            $component->setVariables($option);
        } else {
            $option($component);
        }
        return $component;
    }

    /**
     * 获取当前表格列数量（供插槽定位使用）
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getSlotColumnCount(): int
    {
        return count($this->columns);
    }

    /**
     * 将插槽回调追加的列移动到目标列前/后
     * @param int $addedFrom 插槽回调执行前的列数量
     * @param string $target 目标列键名，不存在时追加到末尾
     * @param bool $after 是否插入到目标列之后
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function applySlotColumn(int $addedFrom, string $target, bool $after): void
    {
        if ($addedFrom >= count($this->columns)) {
            return;
        }
        // 取出本次回调追加的列
        $added = array_splice($this->columns, $addedFrom);
        // 定位目标列
        $index = null;
        foreach ($this->columns as $i => $column) {
            if (is_object($column) && method_exists($column, 'getVariable') && $column->getVariable('name') === $target) {
                $index = $i;
                break;
            }
        }
        $position = $index === null ? count($this->columns) : ($after ? $index + 1 : $index);
        array_splice($this->columns, $position, 0, $added);
    }

    /**
     * 添加表格列
     * @param string $name 列键名
     * @param string $title 列标题
     * @param callable|array $option 列配置
     * @return TableColumn
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addColumn(string $name, string $title, callable|array $option = [])
    {
        $component = $this->useCustomColumn(TableColumn::class, $name, $title, $option);
        return $component;
    }

    /**
     * 获取表格列配置
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * 对表格列进行排序
     * @param callable $callback 排序回调函数，接收两个 TableColumn 参数，返回整数
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function sortColumns(callable $callback): void
    {
        usort($this->columns, $callback);
    }
}
