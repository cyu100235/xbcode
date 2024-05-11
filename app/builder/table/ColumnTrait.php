<?php

namespace app\builder\table;
use app\builder\ListBuilder;
use Exception;

/**
 * 列操作
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ColumnTrait
{
    // 列字段名（注：属性层级越深，渲染性能就越差，例如：aa.bb.cc.dd.ee）
    protected $columns = [];

    /**
     * 列配置
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-03-07
     * @param  array       $columnConfig
     * @return ListBuilder
     */
    public function columnConfig(array $columnConfig = []): ListBuilder
    {
        $columnConfig       = array_merge([
            // 是否需要为每一列的 VNode 设置 key 属性（非特殊情况下不需要使用）
            'useKey'    => false,
            // 当鼠标点击列头时，是否要高亮当前列
            'isCurrent' => false,
            // 	当鼠标移到列头时，是否要高亮当前头
            'isHover'   => true,
            // 每一列是否启用列宽调整
            'resizable' => true,
            // 每一列的宽度 auto, px, %
            'width'     => 'auto',
            // 每一列的最小宽度 auto, px, %
            'minWidth'  => 'auto',
        ], $columnConfig);
        $this->columnConfig = $columnConfig;
        return $this;
    }

    /**
     * 添加列
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-02-27
     * @param  string      $field
     * @param  string      $title
     * @param  array       $extra
     * @return ListBuilder
     */
    public function addColumn(string $field, string $title, array $extra = []): ListBuilder
    {
        $columns         = [
            'field' => $field,
            'title' => $title
        ];
        $this->columns[] = array_merge($extra, $columns);
        return $this;
    }

    /**
     * 添加元素列
     *
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-03-07
     * @param  string      $field
     * @param  string      $title
     * @param  array       $extra
     * @return ListBuilder
     */
    public function addColumnEle(string $field, string $title, array $extra = []): ListBuilder
    {
        // 元素类型
        if (!isset($extra['params']['type'])) {
            throw new Exception('缺少元素类型');
        }
        // 图片类型默认开启预览
        $imageType = ['image','images', 'pictuer'];
        if (in_array($extra['params']['type'],$imageType) && empty($extra['params']['props'])) {
            $extra['params']['props']['preview'] = true;
            $extra['params']['props']['width'] = 40;
            $extra['params']['props']['height'] = 40;
            $extra['params']['props']['fit'] = 'cover';
        }
        $extra = array_merge([
            'slots' => [
                'default' => $extra['params']['type']
            ],
        ], $extra);
        $this->addColumn($field, $title, $extra);
        return $this;
    }

    /**
     * 添加编辑列
     *
     * @Author 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-03-13
     * @param  string      $field
     * @param  string      $title
     * @param  array       $extra
     * @return ListBuilder
     */
    public function addColumnEdit(string $field, string $title, array $extra = []): ListBuilder
    {
        $extra = array_merge_recursive([
            'editRender' => [
                'name'  => 'input',
                'attrs' => [
                    'placeholder' => "请输入{$title}",
                ],
            ],
        ], $extra);
        $this->addColumn($field, $title, $extra);
        return $this;
    }
}