<?php

namespace app\common;

/**
 * 枚举基类
 * @copyright 贵州猿创科技有限公司
 * @Email 416716328@qq.com
 * @DateTime 2023-04-29
 */
abstract class Enum extends EnumBase
{
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
        foreach ($data as $value) {
            $list[$value['value']] = $value['label'];
        }
        return $list;
    }

    /**
     * 获取枚举字典label
     * @param string $label
     * @param string $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function getLabel(string $label,string $default = null)
    {
        $data = self::toArray();
        if (empty($data)) {
            return $default;
        }
        $data = array_column($data,'label','value');
        return $data[$label] ?? $default;
    }

    /**
     * 获取枚举字典value
     * @param string $value
     * @param string $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function getValue(string $value,string $default = null)
    {
        $data = self::toArray();
        if (empty($data)) {
            return $default;
        }
        $data = array_column($data,'value','label');
        return $data[$value] ?? $default;
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
        if (empty($data)) {
            return [];
        }
        return array_column($data,$field);
    }
    
    /**
     * 获取选项数据
     * @param bool|null $disabled
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function options(bool|null $disabled = null): array
    {
        $data = self::toArray();
        $list = [];
        $i    = 0;
        foreach ($data as $value) {
            $list[$i] = [
                'label'     => $value['label'],
                'value'     => $value['value'],
            ];
            if (isset($value['disabled'])) {
                $list[$i]['disabled'] = $value['disabled'];
            }
            if ($disabled !== null) {
                $list[$i]['disabled'] = $disabled;
            }
            $i++;
        }
        return $list;
    }

    /**
     * label映射其他字段名
     * @param string $field
     * @param mixed $other
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function labelMap(string $field,$other = true)
    {
        $data = self::toArray();
        $list = [];
        foreach ($data as $value) {
            $list[$value['value']] = [
                $field      => $value['label'],
            ];
            if ($other) {
                $list[$value['value']]['value'] = $value['value'];
            }
        }
        return $list;
    }
}
