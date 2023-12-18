<?php

namespace app\common\utils;

use app\common\model\Settings;
use Exception;

/**
 * 配置工具类
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
class SettingUtil
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
    public static function getOriginConfig(array $where, mixed $default = null,bool $system = false)
    {
        if ($system) {
            $model = Settings::withoutGlobalScope();
        } else {
            $model = Settings::where($where);
        }
        $value = $model->value('value');
        if (empty($value)) {
            return $default;
        }
        return $value;
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
     * 读取系统级配置项数据
     * @param string $group
     * @param string $name
     * @param mixed $default
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function setting(string $group, string $name = null, mixed $default = null)
    {
        $data = self::getOriginConfig(['name'=> $group],$default,true);
        if (empty($data)) {
            return $default;
        }
        if (empty($name)) {
            if (is_array($data)) {
                return self::getConfigValue($data);
            }
            return $data;
        }
        if (isset($data[$name])) {
            $data = $data[$name];
            if (is_array($data)) {
                # 解析层级数据
                return self::getConfigValue($data);
            }
            return $data;
        }
        # 解析层级数据
        if (is_array($data)) {
            return self::getConfigValue($data);
        }
        return $data;
    }

    /**
     * 获取非系统级配置
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
    
    /**
     * 保存配置项数据
     * @param string $group
     * @param array $data
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function save(string $group, array $data,string $active = null)
    {
        $model = Settings::where(['name' => $group])->find();
        if (!$model) {
            $model = new Settings;
            $model->name = $group;
            if ($active) {
                $model->active = $active;
            }
        }
        $model->value = $data;
        if (!$model->save()) {
            throw new Exception('保存配置项失败');
        }
    }
}