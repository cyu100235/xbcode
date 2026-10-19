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
     * 待重定位的插槽列批次
     *
     * 插槽定位的目标列可能由注册在后的监听器创建（跨插件前向引用），
     * 此时先暂存该批次，待所有监听器执行完毕后由 flushSlotColumns() 统一重定位。
     * @var array<int, array{columns:array, target:string, after:bool}>
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected array $pendingSlotColumns = [];

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
     *
     * 目标列若尚未创建（可能由注册在后的监听器创建），则暂存本批次，
     * 待所有监听器执行完毕后由 flushSlotColumns() 统一重定位。
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
        $index = $this->findColumnIndex($target);
        if ($index === null) {
            // 目标列暂不存在，先暂存批次等待后续监听器补齐目标列
            $this->pendingSlotColumns[] = [
                'columns' => $added,
                'target'  => $target,
                'after'   => $after,
            ];
            // 暂存期间先按追加顺序放在末尾，避免丢失列
            $this->columns = array_merge($this->columns, $added);
            return;
        }
        $position = $after ? $index + 1 : $index;
        array_splice($this->columns, $position, 0, $added);
    }

    /**
     * 落定所有暂存的插槽列批次
     *
     * 在所有插槽监听器执行完毕后调用：此时其他插件注册的列均已创建，
     * 暂存批次可正确重定位到目标列前/后；目标列仍不存在则保留在末尾。
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function flushSlotColumns(): void
    {
        if (empty($this->pendingSlotColumns)) {
            return;
        }
        $pending = array_values($this->pendingSlotColumns);
        $this->pendingSlotColumns = [];
        // 按对象标识摘除所有暂存列，再按注册顺序依次重定位到目标列前/后
        $stashed = [];
        foreach ($pending as $item) {
            foreach ($item['columns'] as $column) {
                $stashed[] = $column;
            }
        }
        $this->columns = array_values(array_filter($this->columns, function ($column) use ($stashed) {
            return !in_array($column, $stashed, true);
        }));
        foreach ($pending as $item) {
            $index = $this->findColumnIndex($item['target']);
            $position = $index === null ? count($this->columns) : ($item['after'] ? $index + 1 : $index);
            array_splice($this->columns, $position, 0, $item['columns']);
        }
    }

    /**
     * 查找列集合中指定键名的列下标
     * @param string $name 列键名
     * @return int|null 未找到返回 null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function findColumnIndex(string $name): ?int
    {
        foreach ($this->columns as $i => $column) {
            if (is_object($column) && method_exists($column, 'getVariable') && $column->getVariable('name') === $name) {
                return $i;
            }
        }
        return null;
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
