<?php
namespace plugin\xbCode\base;

use plugin\xbCode\builder\Components\Tag;

/**
 * 枚举基类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class BaseEnum
{
    /**
     * 获取开关枚举
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function switch()
    {
        $data = [
            'activeText' => static::getLabel('20'),
            'inactiveText' => static::getLabel('10'),
            'activeValue' => static::getValue('20'),
            'inactiveValue' => static::getValue('10'),
        ];
        return $data;
    }

    /**
     * 获取样式枚举
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function style()
    {
        $data = static::toArray();
        $list = [];
        foreach ($data as $value) {
            $list[$value['value']] = [
                'type' => $value['label'],
            ];
        }
        return $list;
    }
    
    /**
     * 获取字典枚举
     * @param array $elements 元素替换
     * @param bool $isElement 是否是元素
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function dict(array $elements = [],bool $isElement = true)
    {
        $data = static::toArray();
        $data = array_column($data, 'label', 'value');
        if($isElement && empty($elements)) {
            $elements = [
                '10' => "<span class='label label-warning'>{value}</span>",
                '20' => "<span class='label label-success'>{value}</span>",
                '30' => "<span class='label label-danger'>{value}</span>",
                '40' => "<span class='label label-info'>{value}</span>",
                '50' => "<span class='label label-default'>{value}</span>",
            ];
        }
        if (!empty($elements)) {
            foreach ($data as $key => $value) {
                if (isset($elements[$key])) {
                    $valueElement = $elements[$key];
                    $data[$key] = str_replace("{value}", $value, $valueElement);
                }
            }
        }
        return $data;
    }

    /**
     * 获取选项枚举
     * @param callable $callback
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function options(callable $callback = null)
    {
        $data = static::toArray();
        if ($callback && is_callable($callback)) {
            $data = array_map($callback, $data);
        }
        return $data;
    }

    /**
     * 获取值枚举列
     * @param string $name 列名
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getColumn(string $name)
    {
        $data = static::toArray();
        $data = array_column($data, $name);
        return $data;
    }

    /**
     * 获取枚举值
     * @param string $value
     * @param mixed $default
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getValue(string $value, mixed $default = null)
    {
        $data = static::toArray();
        $dict = array_column($data, null, 'value');
        return isset($dict[$value]['value']) ? $dict[$value]['value'] : $default;
    }

    /**
     * 获取枚举键
     * @param string $value
     * @param mixed $default
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getLabel(string $value, mixed $default = null)
    {
        $data = static::toArray();
        $dict = array_column($data, null, 'value');
        return isset($dict[$value]['label']) ? $dict[$value]['label'] : $default;
    }

    /**
     * 转换为数组
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function toArray()
    {
        $data = static::getEnumData();
        $list = [];
        foreach ($data as $name => $label) {
            // 键转小写
            $keyName = strtolower($name);
            // 拆分_to_
            if (strpos($name, '_to_') === false) {
                $temp = explode('_to_', $keyName);
                $temp = array_filter($temp);
                $temp = end($temp);
                $keyName = $temp;
            }
            // 替换_tob_
            if (strpos($name, '_tob_') === false) {
                // 替换TOB_为斜杠
                $temp = str_replace('_tob_', '/', $keyName);
                $keyName = $temp;
            }

            // 添加到列表
            $list[] = [
                "name" => $name,
                'label' => $label,
                "value" => (string) $keyName,
            ];
        }
        return $list;
    }

    /**
     * 获取枚举数据
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function getEnumData()
    {
        // 反射当前类
        $reflect = new \ReflectionClass(static::class);
        // 获取常量
        $data = $reflect->getConstants();
        // 返回数据
        return $data;
    }
}