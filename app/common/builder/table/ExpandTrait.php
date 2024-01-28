<?php

namespace app\common\builder\table;
use app\common\builder\ListBuilder;

/**
 * 筛选查询
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ExpandTrait
{
    /**
     * 展开配置（不支持虚拟滚动）
     *
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array       $config
     * @return ListBuilder
     */
    public function expandConfig(array $config = []): ListBuilder
    {
        $this->expandConfig = array_merge([
            // 展开列显示的字段名，可以直接显示在单元格中
            'labelField'    => 'id',
            // 默认展开所有行（只会在初始化时被触发一次）
            'expandAll'     => true,
            // 默认展开指定行（只会在初始化时被触发一次，需要有 row-config.keyField）
            'expandRowKeys' => [],
            // 每次只能展开一行
            'accordion'     => false,
            // 展开内容的高度，默认自适应高度
            'height'        => '',
            /**
             * 触发方式（注：当多种功能重叠时，会同时触发）
             * default（点击按钮触发）, cell（点击单元格触发）, row（点击行触发）
             */
            'trigger'       => 'cell',
            // 是否使用懒加载
            'lazy'          => false,
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