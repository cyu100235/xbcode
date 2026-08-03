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
namespace plugin\xbCode\builder\Renders\form\rows;

use plugin\xbCode\builder\Components\Form\FieldSet;

/**
 * 标题表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait FieldSetRow
{
    /**
     * 添加标题分割组件
     * @return FieldSet
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowFieldSet(string $title, array $components)
    {
        // 排除重复的表单项（基于名称）
        $this->excludeFormRows($components);
        
        // 过滤掉已经存在于 formRows 中的组件对象
        $filteredComponents = [];
        foreach ($components as $component) {
            if (is_object($component)) {
                // 检查该对象是否已经在 formRows 中
                $exists = false;
                foreach ($this->formRows as $existingRow) {
                    if ($existingRow === $component) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $filteredComponents[] = $component;
                }
            } else {
                // 数组形式的组件直接保留
                $filteredComponents[] = $component;
            }
        }
        
        /** @var FieldSet */
        $component = $this->getRowComponent(FieldSet::class, '', '');
        $component->title($title);
        $component->body($filteredComponents);
        $this->formRows[] = $component;
        return $component;
    }
}
