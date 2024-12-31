<?php
namespace xbcode\builder\form;

use xbcode\builder\FormBuilder;
use FormBuilder\Driver\CustomComponent;

/**
 * 选项卡表单构建器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait TabsFormTrait
{
    /**
     * 初始化选项卡表单
     * @param string $active
     * @param array $extra
     * @return FormBuilder
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     * @email cy958416459@qq.com
     */
    public function initTabs(string $active, array $extra = []): FormBuilder
    {
        // 初始化选项卡选中
        $this->initTabsActive('', $active, $extra);
        // 返回资源对象
        return $this;
    }

    /**
     * 初始化选项卡选中
     * @param string $field
     * @param string $active
     * @param array $extra
     * @return FormBuilder
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     * @email cy958416459@qq.com
     */
    public function initTabsActive(string $field,string $active, array $extra = []): FormBuilder
    {
        // 选项卡组件
        $component = new CustomComponent('el-tabs');
        if ($field) {
            $component->field($field);
        }
        // 设置默认选中
        $component->value($active);
        // 设置选项卡组件属性
        $component->props([
            'modelValue'    => $active,
            'type'          => 'border-card'
        ]);
        foreach ($extra as $componentType => $componentValue) {
            $component->$componentType($componentValue);
        }
        $this->tabBuilder = $component;
        // 返回资源对象
        return $this;
    }

    /**
     * 添加子选项卡数据
     * @param string $field
     * @param string $title
     * @param array $children
     * @return FormBuilder
     * @throws \Exception
     */
    public function addTab(string $field, string $title, array $children): FormBuilder
    {
        return $this->addPanel($field, $title, $children);
    }

    /**
     * 添加子面板数据
     * @param string $field
     * @param string $title
     * @param array $children
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function addPanel(string $field, string $title, array $children): FormBuilder
    {
        $component[] = [
            'type'          => 'el-tab-pane',
            'props'         => [
                'name'      => $field,
                'label'     => $title,
            ],
            'children'      => $children
        ];
        $this->tabBuilder->appendChildren($component);
        // 返回资源对象
        return $this;
    }

    /**
     * 结束选项卡表单
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-04-29
     */
    public function endTabs(): FormBuilder
    {
        $this->builder->append($this->tabBuilder);
        return $this;
    }
}