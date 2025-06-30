<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\builder\Components\Form;

/**
 * 输入键值对组件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/form/input-kvs
 * @method $this valueType(string $value) 键值对类型
 * @method $this keyPlaceholder(string $value) 键输入框占位符
 * @method $this valuePlaceholder(string $value) 值输入框占位符
 * @method $this defaultValue(string $value) 默认值
 * @method $this draggable(string $value) 是否可拖拽
 * @method $this addButtonText(string $value) 添加按钮文本
 * @method $this keyItem(string $value) 键输入框配置
 * @method $this valueItems(string $value) 值输入框配置
 */
class InputKVS extends FormBase
{
    public string $type = 'input-kvs';

    public function getValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $v) {
                if (is_array($value)) {
                    foreach ($v as $k => $item) {
                        $component = collect($this->valueItems)->firstWhere('name', $k);
                        if (is_object($component) && method_exists($component::class, 'getValue')) {
                            data_set($value, $key . '.' . $k, $component->getValue($item));
                        }
                    }
                } else {
                    $component = collect($this->valueItems)->firstWhere('name', $key);
                    if (is_object($component) && method_exists($component::class, 'getValue')) {
                        data_set($value, $key, $component->getValue($value));
                    }
                }
            }
        }
        return $value;
    }

    public function setValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $v) {
                if (is_array($value)) {
                    foreach ($v as $k => $item) {
                        $component = collect($this->valueItems)->firstWhere('name', $k);
                        if (is_object($component) && method_exists($component::class, 'setValue')) {
                            data_set($value, $key . '.' . $k, $component->setValue($item));
                        }
                    }
                } else {
                    $component = collect($this->valueItems)->firstWhere('name', $key);
                    if (is_object($component) && method_exists($component::class, 'setValue')) {
                        data_set($value, $key, $component->setValue($value));
                    }
                }
            }
        }
        return $value;
    }

    public function onDelete($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $v) {
                if (is_array($value)) {
                    foreach ($v as $k => $item) {
                        $component = collect($this->valueItems)->firstWhere('name', $k);
                        if (is_object($component) && method_exists($component::class, 'onDelete')) {
                            data_set($value, $key . '.' . $k, $component->onDelete($item));
                        }
                    }
                } else {
                    $component = collect($this->valueItems)->firstWhere('name', $key);
                    if (is_object($component) && method_exists($component::class, 'onDelete')) {
                        data_set($value, $key, $component->onDelete($value));
                    }
                }
            }
        }
        return $value;
    }
}
