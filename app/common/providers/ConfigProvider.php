<?php

namespace app\common\providers;

use app\model\Settings;
use app\model\Upload;
use Exception;

/**
 * 配置项提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ConfigProvider
{
    /**
     * 获取配置项
     * @param string $group
     * @param string|array $name
     * @param mixed $default
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get(string $group, string|array $name = '', $default = null)
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
     * 保存配置项
     * @param string $group
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function save(string $group, array $data)
    {
        foreach ($data as $field => &$value) {
            $where = [
                'group' => $group,
                'name'  => $field,
            ];
            $model = Settings::where($where)->find();
            if (!$model) {
                $model        = new Settings;
                $model->name  = $field;
                $model->group = $group;
            }
            // 检测是否文件
            if (is_array($value) && $path = UploadProvider::path($value)) {
                $value = $path;
            }
            if (!$model->save(['value' => $value])) {
                throw new Exception('保存失败');
            }
        }
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
                // 解析层级键值
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
    public static function getOriginal(string $group, $default = null)
    {
        $data = Settings::where('group', $group)->column('value', 'name');
        if (empty($data)) {
            return $default;
        }
        return $data;
    }

    /**
     * 解析数据
     * @param array $data
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function parseData(array $data)
    {
        foreach ($data as $name => $value) {
            if (is_string($value)) {
                // 是否附件
                if (Upload::where('path', $value)->count()) {
                    $url         = UploadProvider::url($value);
                    $data[$name] = $url;
                }
            }
        }
        return $data;
    }
}