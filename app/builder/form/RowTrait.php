<?php

namespace app\builder\form;

use app\builder\FormBuilder;
use FormBuilder\Factory\Elm;
use FormBuilder\Driver\CustomComponent;

/**
 * 表单行
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait RowTrait
{
    /**
     * 添加表单行元素
     * @param string $field
     * @param string $type
     * @param string $title
     * @param mixed $value
     * @param array $extra
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @Email cy958416459@qq.com
     * @DateTime 2023-05-05
     */
    public function addRow(string $field, string $type, string $title, $value = '', array $extra = []): FormBuilder
    {
        if (method_exists(Elm::class, $type)) {
            // 创建普通组件
            $component = Elm::$type($field, $title, $value);
        } else {
            // 创建自定义组件
            $component = new CustomComponent($type);
            // 设置字段，默认数据等
            $component->field($field)->title($title)->value($value);
        }
        // 设置组件提示语
        if (isset($extra['prompt'])) {
            $promptData = $extra['prompt'];
            unset($extra['prompt']);
            $prompt['type'] = 'prompt-tip';
            // 设置默认提示语
            if (is_array($promptData) && isset($promptData['text'])) {
                $prompt['props'] = $promptData;
                unset($prompt['text']);
            }
            // 设置字符提示语
            if (is_string($promptData) && !empty($promptData)) {
                $prompt['props']['text'] = $promptData;
            }
            // 插入组件
            $component->appendRule('suffix',$prompt);
        }
        // 设置组件属性
        foreach ($extra as $componentType => $componentValue) {
            $component->$componentType($componentValue);
        }
        // 设置组件
        $this->builder->append($component);
        // 返回组件
        return $this;
    }
}