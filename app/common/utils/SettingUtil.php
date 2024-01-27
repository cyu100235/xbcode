<?php

namespace app\common\utils;

use app\common\model\Settings;
use Exception;

/**
 * 配置项工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SettingUtil
{
    /**
     * 获取配置项
     * @param string $group
     * @param mixed $default
     * @param string|array $name
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function config(string $group, $default = null,string|array $name ='')
    {
        $data = self::getOriginal($group);
        // 没有配置项
        if (empty($data)) {
            return $default;
        }
        // 取出指定配置项
        if (is_array($name)) {
            $result = [];
            foreach ($name as $value) {
                $result[$value] = $data[$value] ?? $default;
            }
            // 解析配置项键值对
            $result = self::getConfigValue($result);
            // 返回配置数据
            return $result;
        }
        // 取出指定配置项
        if ($name) {
            return $data[$name] ?? $default;
        }
        // 解析配置项键值对
        $data = self::getConfigValue($data);
        // 返回所有配置项
        return $data;
    }
    
    /**
     * 获取配置项数据
     * @param array $data
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getConfigValue(array $data)
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
     * 组装为层级值
     * @param array $data
     * @param mixed $config
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function createNestedArray(array $data, mixed $config)
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
     * 获取原始数据值
     * @param string|null $group
     * @param mixed $default
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getOriginal(string $group,$default = null)
    {
        $data = Settings::where('group',$group)->column('value','name');
        if (empty($data)) {
            return $default;
        }
        foreach ($data as $field => $value) {
            // 检测是否图片文件
            if (strpos($value, '[xbase]') !== false) {
                $data[$field] = explode('[xbase]', $value);
            }
        }
        return $data;
    }

    /**
     * 保存配置项
     * @param string $group
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function save(string $group,array $data)
    {
        foreach ($data as $field => $value) {
            $where  = [
                'group' => $group,
                'name'  => $field,
            ];
            $model = Settings::where($where)->find();
            if (!$model) {
                $model       = new Settings;
                $model->name = $field;
                $model->group = $group;
            }
            // 检测是否文件
            if (is_array($value)) {
                if (count($value) > 1) {
                    $value = implode('[xbase]', $value);
                } else {
                    $value = current($value);
                }
            }
            $model->value = $value;
            if (!$model->save()) {
                throw new Exception('保存失败');
            }
        }
    }
}