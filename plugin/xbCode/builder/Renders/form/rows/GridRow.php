<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\GridSchema;

/**
 * Grid布局表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait GridRow
{
    /**
     * 添加Grid布局组件
     * @param array $components 组件数组
     * @return GridSchema
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowGrid(array $components = [])
    {
        /** @var GridSchema */
        $component = GridSchema::make();
        $component->style([
            'margin-bottom' => 'var(--Form-item-gap)',
        ]);
        
        // 收集传入的组件对象ID，这些是需要从 formRows 中移出的
        $inputIds = [];
        foreach ($components as $rowComponent) {
            if (is_object($rowComponent)) {
                $inputIds[] = spl_object_id($rowComponent);
            }
        }
        
        // 将传入的组件从 formRows 中移出（它们会被添加到 Grid 中）
        $newFormRows = [];
        foreach ($this->formRows as $existingRow) {
            if (is_object($existingRow) && in_array(spl_object_id($existingRow), $inputIds)) {
                continue; // 跳过传入的组件
            }
            $newFormRows[] = $existingRow;
        }
        $this->formRows = array_values($newFormRows);
        
        // 将 Grid 组件添加到 formRows
        $this->formRows[] = $component;
        
        // 将组件数组转换为Grid的columns配置
        $columns = [];
        foreach ($components as $rowComponent) {
            if (is_object($rowComponent)) {
                $columns[] = [
                    'valign' => 'middle',
                    'body' => [$rowComponent]
                ];
            } else if (is_array($rowComponent)) {
                $columns[] = $rowComponent;
            }
        }
        
        if (!empty($columns)) {
            $component->columns($columns);
        }
        
        return $component;
    }
}
