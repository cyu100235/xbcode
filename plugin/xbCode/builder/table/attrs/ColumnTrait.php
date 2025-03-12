<?php

namespace plugin\xbCode\builder\table\attrs;

use plugin\xbCode\builder\ListBuilder;

/**
 * 表格列操作
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ColumnTrait
{
    // 列字段名（注：属性层级越深，渲染性能就越差，例如：aa.bb.cc.dd.ee）
    protected $columns = [];
    
    /**
     * 列配置
     * @param array $columnConfig
     * @return ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function columnConfig(array $columnConfig = []): ListBuilder
    {
        $columnConfig = array_merge([
            // 是否需要为每一列的 VNode 设置 key 属性（非特殊情况下不需要使用）
            'useKey' => false,
            // 当鼠标点击列头时，是否要高亮当前列
            'isCurrent' => false,
            // 	当鼠标移到列头时，是否要高亮当前头
            'isHover' => true,
            // 每一列是否启用列宽调整
            'resizable' => true,
            // 每一列的宽度 auto, px, %
            'width' => 'auto',
            // 每一列的最小宽度 auto, px, %
            'minWidth' => 'auto',
        ], $columnConfig);
        $this->columnConfig = $columnConfig;
        return $this;
    }
    
    /**
     * 添加表格单元列
     * @param string $field
     * @param string $title
     * @param array $extra
     * @return ListBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function addColumn(string $field, string $title, array $extra = []): ListBuilder
    {
        $columns = [
            'field' => $field,
            'title' => $title
        ];
        // 列类型
        if (isset($extra['params']['type']) && !empty($extra['params']['type'])) {
            $columns['slots'] = [
                'default' => $extra['params']['type']
            ];
            unset($extra['params']['type']);
        }
        $columns = array_merge($extra, $columns);
        $this->columns[] = $columns;
        return $this;
    }
}