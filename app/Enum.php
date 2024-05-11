<?php
namespace app;

use Exception;

/**
 * 枚举基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
abstract class Enum extends EnumBase
{
    /**
     * 验证枚举值是否存在
     * @param mixed $value
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function hasValue(mixed $value)
    {
        $data    = self::toArray();
        $columns = array_column($data, 'value');
        if (in_array($value, $columns))
        {
            return true;
        }
        return false;
    }

    /**
     * 获取枚举字典值
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function dict()
    {
        $data = self::toArray();
        $list = [];
        foreach ($data as $value)
        {
            $list[$value['value']] = $value['label'];
        }
        return $list;
    }

    /**
     * 获取枚举字典label
     * @param string $label
     * @param string $default
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLabel(string $label, string $default = null)
    {
        $data = self::toArray();
        if (empty($data))
        {
            return $default;
        }
        $data = array_column($data, 'label', 'value');
        return $data[$label] ?? $default;
    }

    /**
     * 获取枚举字典value
     * @param string $value
     * @param string $default
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getValue(string $value, string $default = null)
    {
        $data = self::toArray();
        if (empty($data))
        {
            return $default;
        }
        $data = array_column($data, 'value', 'label');
        return $data[$value] ?? $default;
    }

    /**
     * 获取枚举全部数据
     * @param string $value
     * @param string $field
     * @param string $default
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getEnumData(string $value, string $field = 'value', string $default = null)
    {
        $data = self::toArray();
        if (empty($data))
        {
            return $default;
        }
        // 转换字符大写
        $value = strtoupper($value);
        // 获取数据
        return $data[$value] ?? [];
    }

    /**
     * 获取枚举列数据
     * @param string $field
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function columns(string $field)
    {
        $data = self::toArray();
        if (empty($data))
        {
            return [];
        }
        return array_column($data, $field);
    }

    /**
     * 获取选项数据
     * @param bool|null $disabled
     * @param string $label
     * @param string $valField
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function options(bool|null $disabled = null,string $label = 'label',string $valField = 'value'): array
    {
        $data = self::toArray();
        $list = [];
        $i    = 0;
        foreach ($data as $value)
        {
            $list[$i] = [
                $label => $value['label'],
                $valField => $value['value'],
            ];
            if (isset($value['disabled']))
            {
                $list[$i]['disabled'] = $value['disabled'];
            }
            if ($disabled !== null)
            {
                $list[$i]['disabled'] = $disabled;
            }
            $i++;
        }
        return $list;
    }

    /**
     * 获取选中枚举KEY数据
     * @param mixed $key
     * @param string $field
     * @param string $valField
     * @throws \Exception
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getActiveEnum(mixed $key, string $field = 'label',string $valField = 'value')
    {
        $data = self::toArray();
        if (!isset($data[$key])) {
            throw new Exception('枚举字典KEY不存在');
        }
        return [
            $field      => $data[$key]['label'],
            $valField   => $data[$key]['value'],
        ];
    }

    /**
     * label映射其他字段名
     * @param string $field
     * @param mixed $other
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function labelMap(string $field, $other = true)
    {
        $data = self::toArray();
        $list = [];
        foreach ($data as $value)
        {
            $list[$value['value']] = [
                $field => $value['label'],
            ];
            if ($other)
            {
                $list[$value['value']]['value'] = $value['value'];
            }
        }
        return $list;
    }
}
