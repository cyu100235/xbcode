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
namespace plugin\xbCode\api;

use Exception;
use plugin\xbUpload\api\Files;

/**
 * 配置数据处理接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ConfigChecked
{
    /**
     * 替换键名
     * @param string $name
     * @param array $data
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function replaceKeys(string $name, array $data)
    {
        $list = [];
        foreach ($data as $field => $value) {
            $field = preg_replace("/{$name}/", '', $field, 1);
            if (empty($field)) {
                continue;
            }
            $list[$field] = $value;
        }
        return $list;
    }

    /**
     * 替换文件URL
     * @param array $data
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function replaceFileUrl(array $data)
    {
        foreach ($data as $key => $value) {
            if ($value && strpos($value, 'attachment/') !== false) {
                $data[$key] = Files::url($value);
            }
        }
        return $data;
    }

    /**
     * 解析配置层级
     * @param array $data
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getConfigValue(array $data)
    {
        $configValue = [];
        foreach ($data as $field => $value) {
            if (strrpos($field, '.') !== false) {
                // 解析层级键值
                $dataField = explode('.', $field);
                $resutil = self::createNestedArray($dataField, $value);
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function createNestedArray(array $data, mixed $config)
    {
        $data2 = [];
        $current = &$data2;
        foreach ($data as $field) {
            $current = &$current[$field];
        }
        $current = $config;
        return $data2;
    }

    /**
     * 获取插件名称
     * @param string $path
     * @throws \Exception
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getPluginName(string $path)
    {
        if (str_contains($path, '/')) {
            $parts = explode('/', $path);
            return current($parts);
        }
        if (str_contains($path, '.')) {
            $parts = explode('.', $path);
            return current($parts);
        }
        throw new Exception("{$path} - 插件名称获取失败");
    }

    /**
     * 获取分组名称
     * @param string $path
     * @throws \Exception
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getGroupName(string $path)
    {
        if (str_contains($path, '/')) {
            $parts = explode('/', $path);
            return end($parts);
        }
        if (str_contains($path, '.')) {
            $parts = explode('.', $path);
            return end($parts);
        }
        throw new Exception("{$path} - 分组名称获取失败");
    }
}