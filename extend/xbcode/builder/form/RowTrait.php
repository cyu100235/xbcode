<?php

namespace xbcode\builder\form;

use xbcode\builder\FormBuilder;
use Exception;
use FormBuilder\Factory\Elm;
use FormBuilder\Driver\CustomComponent;
use FormBuilder\Form;

/**
 * 表单行
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait RowTrait
{
    /**
     * 表单对象
     * @var Form
     */
    private $builder;

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
    public function addRow(string $field, string $type, string $title, mixed $value = '', array $extra = []): FormBuilder
    {
        // 检测是否选项组件
        if (in_array($type, ['checkbox', 'radio', 'select'])) {
            if (!isset($extra['options'])) {
                throw new Exception("[{$title}] - 选项扩展options数据必须设置");
            }
            if (empty($value) && is_array($value)) {
                $value = [];
            }
        }
        // 创建组件
        if (method_exists(Elm::class, $type)) {
            // 创建普通组件
            $component = Elm::$type($field, $title, $value);
        } else {
            // 创建自定义组件
            $component = new CustomComponent($type);
        }
        // 设置字段，默认数据等
        $component->field($field)->title($title)->value($value);
        // 设置组件提示语
        if (isset($extra['prompt'])) {
            // 组件样式
            $style = $extra['style'] ?? [];
            $prompt = [
                'type' => 'xbPrompt',
                'props' => [
                    'text' => $extra['prompt'],
                    'xStyle' => $style,
                ],
            ];
            // 删除提示语属性
            if (isset($extra['prompt'])) {
                unset($extra['prompt']);
            }
            // 删除样式属性
            if (isset($extra['style'])) {
                unset($extra['style']);
            }
            // 插入组件
            $component->appendRule('suffix', $prompt);
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