<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 筛选查询
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait TreeTrait
{
    /**
     * 树表格配置
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function treeConfig(array $config = []): ListBuilder
    {
        $this->treeConfig = array_merge([
            // 自动将列表转为树结构（支持虚拟滚动）
            'transform'     => true,
            // 树节点的字段名
            'rowField'      => 'id',
            // 树父节点的字段名
            'parentField'   => 'pid',
            // 树子节点的字段名
            // 'children'          => 'children',
            /**
             * 树节点的缩进
             * 需要在addColumn方法中的扩展参数内添加treeNode标注为节点
             */
            'indent'        => 20,
            // 树节点的连接线（启用连接线会降低渲染性能）
            'line'          => false,
            // 默认展开所有子孙树节点（只会在初始化时被触发一次）
            'expandAll'     => true,
            // 默认展开指定树节点（只会在初始化时被触发一次，需要有 row-config.keyField）
            'expandRowKeys' => [],
            // 对于同一级的节点，每次只能展开一个
            'accordion'     => false,
            /**
             * 触发方式（注：当多种功能重叠时，会同时触发）
             * default（点击按钮触发）, cell（点击单元格触发）, row（点击行触发）
             */
            'trigger'       => 'cell',
            // 是否使用懒加载（启用后只有指定 hasChild 的节点才允许被点击）
            'lazy'          => false,
            // 只对 lazy 启用后有效，标识是否存在子节点，从而控制是否允许被点击
            'hasChild'      => 'hasChild',
            /**
             * 是否保留展开状态
             * 对于某些场景可能会用到
             * 比如数据被刷新之后还保留之前展开的状态
             * 需要有 row-config.keyField
             */
            'reserve'       => false,
            // 是否显示图标按钮
            'showIcon'      => true,
            // 自定义展开后显示的图标
            'iconOpen'      => 'vxe-icon-square-minus',
            // 自定义收起后显示的图标
            'iconClose'     => 'vxe-icon-square-plus',
            // 自定义懒加载中显示的图标
            'iconLoaded'    => '',
        ], $config);
        return $this;
    }
}