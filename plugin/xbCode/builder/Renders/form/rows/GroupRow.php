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

use plugin\xbCode\builder\Components\Form\Group;

/**
 * 分组表单项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait GroupRow
{
    /**
     * 添加表单项分组
     * @param array $components
     * @return Group
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function addRowGroup(string $name, array $components)
    {
        // 第一步：找出已在 formRows 中的组件对象（通过对象引用比较）
        $componentsInFormRows = [];
        foreach ($components as $component) {
            if (is_object($component)) {
                foreach ($this->formRows as $existingRow) {
                    if ($existingRow === $component) {
                        $componentsInFormRows[] = $component;
                        break;
                    }
                }
            }
        }
        
        // 第二步：从 formRows 中移除这些组件（基于对象引用）
        if (!empty($componentsInFormRows)) {
            $this->formRows = array_filter($this->formRows, function ($row) use ($componentsInFormRows) {
                foreach ($componentsInFormRows as $comp) {
                    if ($row === $comp) {
                        return false;
                    }
                }
                return true;
            });
            $this->formRows = array_values($this->formRows);
        }
        
        // 第三步：排除其他重复的表单项（基于名称，处理数组形式的组件）
        $this->excludeFormRows($components);
        
        // 第四步：将所有组件添加到 filteredComponents
        $filteredComponents = [];
        foreach ($components as $component) {
            $filteredComponents[] = $component;
        }
        
        // 第五步：创建 Group 组件
        /** @var Group */
        $component = Group::make();
        $component->setVariable('groupName', $name);
        $component->body($filteredComponents);
        $this->formRows[] = $component;
        return $component;
    }
}