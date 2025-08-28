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
namespace plugin\xbCode\base;

use Exception;

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
            'onText' => static::getLabel('20'),
            'offText' => static::getLabel('10'),
            'trueValue' => static::getValue('20'),
            'falseValue' => static::getValue('10'),
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
     * 获取枚举字段值
     * @param string $field
     * @throws \Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function dict(string $field = 'style')
    {
        $data = static::toArray();
        if (empty($data)) {
            throw new Exception('枚举类 ' . static::class . ' 没有定义枚举');
        }
        foreach($data as $value){
            if (empty($value[$field])) {
                throw new Exception('枚举类 ' . static::class . ' 没有定义样式');
            }
        }
        $data = array_column($data, $field, 'value');
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
        $className = basename(str_replace("\\", '/', get_called_class()));
        $data = static::getEnumData();
        $list = [];
        foreach ($data as $name => $value) {
            if (!isset($value['label'])) {
                throw new Exception("{$className} 枚举错误，缺少label字段");
            }
            if (!isset($value['value'])) {
                throw new Exception("{$className} 枚举错误，缺少value字段");
            }
            // 追加键名
            $value['key'] = $name;
            // 添加到列表
            $list[] = $value;
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