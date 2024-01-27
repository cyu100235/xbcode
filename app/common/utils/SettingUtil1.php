<?php

namespace app\common\utils;

use app\common\model\Settings;

/**
 * 配置工具类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class SettingUtil1
{
    /**
     * 组装为层级值
     * @param array $data
     * @param mixed $value
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    private static function createNestedArray(array $data, mixed $config)
    {
        $data2   = [];
        $current = &$data2;
        foreach ($data as $field) {
            $current = &$current[$field];
        }
        $current = $config;
        return $data2;
    }

    /**
     * 获取配置项数据
     * @param array $data
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getConfigValue(array $data)
    {
        $configValue = [];
        foreach ($data as $field => $value) {
            if (strrpos($field, '.') !== false) {
                # 解析层级键值
                $dataField   = explode('.', $field);
                $resutil     = self::createNestedArray($dataField, $value);
                $configValue = array_merge_recursive($configValue, $resutil);
            } else {
                $configValue[$field] = $value;
            }
        }
        return $configValue;
    }

    /**
     * 获取原始未解析配置项数据
     * @param array $where
     * @param mixed $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function getOriginConfig(array $where, mixed $default = null)
    {
        $model = Settings::where($where)->find();
        if (!$model) {
            return $default;
        }
        return $model['value'];
    }

    /**
     * 获取选中项
     * @param string $group
     * @param mixed $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function getActive(string $group, mixed $default = null)
    {
        $value = Settings::where('name',$group)->value('active');
        if (empty($value)) {
            return $default;
        }
        return $value;
    }

    /**
     * 获取解析后数据格式配置
     * @param string $group
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function config(string $group, string $name = null, mixed $default = null)
    {
        $data = self::getOriginConfig(['name' => $group]);
        if (empty($data)) {
            return $default;
        }
        if (empty($name)) {
            $data = self::getConfigValue($data);
            return $data;
        }
        if (isset($data[$name])) {
            $data = $data[$name];
            # 解析层级数据
            $data = self::getConfigValue($data);
            return $data;
        }
        # 解析层级数据
        $data = self::getConfigValue($data);
        return $data;
    }

    /**
     * 获取选中配置项数据
     * @param string $group
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function active(string $group, string $name = null, mixed $default = null)
    {
        $data = self::getOriginConfig(['name' => $group]);
        if (empty($data)) {
            return $default;
        }
        $active = self::getActive($group);
        $data = $data[$active] ?? [];
        if (empty($data)) {
            return $default;
        }
        $data = self::getConfigValue($data);
        return $data;
    }
}