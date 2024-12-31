<?php

namespace xbcode\providers\config;

use think\facade\Db;
use xbcode\providers\FileProvider;
use Exception;

/**
 * 配置表单视图虚类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ConfigAttrTrait
{
    /**
     * 保存配置
     * @param string $group
     * @param string $name
     * @param mixed $value
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function saveConfig(string $group, string $name, mixed $value)
    {
        $original = $value;
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        $where = [
            'group' => $group,
            'name' => $name,
        ];
        $data = Db::name('config')->where($where)->find();
        if (empty($data)) {
            Db::name('config')->save([
                'create_at' => date('Y-m-d H:i:s'),
                'update_at' => date('Y-m-d H:i:s'),
                'group' => $group,
                'name' => $name,
                'value' => $value
            ]);
        } else {
            Db::name('config')->where($where)->save([
                'update_at' => date('Y-m-d H:i:s'),
                'value' => $value
            ]);
        }
        return $original;
    }

    /**
     * 解析配置项层级
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
     * 获取模板数据
     * @param string $path 配置文件路径
     * @param array $data 配置数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getTemplateData(string $path, array $data)
    {
        $template = ConfigViewTrait::getConfigTemplate($path);
        if (empty($template)) {
            throw new Exception('配置模板规则数据错误');
        }
        // 处理数据
        foreach ($template as $value) {
            // 获取字段名称
            $field = $value['field'];
            // 获取填写数据
            $dataValue = $data[$field] ?? '';
            // 获取组件类型
            $component = $value['type'] ?? '';
            // 检测组件不存在则抛出异常
            if (empty($component)) {
                throw new Exception('配置模板组件不存在');
            }
            // 检测是否附件
            if ($component === 'xbUpload') {
                $filePath     = empty($dataValue) ? $value['value'] : $dataValue;
                $url          = FileProvider::url($filePath);
                if (is_array($url)) {
                    $url = count($url) === 1? current($url) : $url;
                }
                $data[$field] = $url;
            }
            // 检测是否选项值
            if (in_array($component, ['checkbox', 'radio', 'select'])) {
                if (empty($dataValue)) {
                    $data[$field] = [];
                } else {
                    $data[$field] = json_decode($dataValue, true);
                }
            }
            // 检测是否JSON
            $jsonData = json_decode($dataValue, true);
            if (!is_null($jsonData)) {
                $data[$field] = $jsonData;
            }
        }
        // 返回数据
        return $data;
    }
    
    /**
     * 设置模板数据保存
     * @param string $path 配置文件路径
     * @param array $data 配置数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function setTemplateData(string $path, array $data)
    {
        // 获取模板规则
        $template = ConfigViewTrait::getConfigTemplate($path);
        if (empty($template)) {
            throw new Exception('配置模板规则数据错误');
        }
        // 处理数据
        foreach ($template as $value) {
            // 获取字段名称
            $field = $value['field'];
            // 获取填写参数
            $dataValue = $data[$field] ?? '';
            // 验证组件类型
            $component = $value['type'];
            if (empty($component)) {
                throw new Exception('配置模板组件不存在');
            }
            // 检测是否alert组件
            if ($component === 'xbAlert' && isset($data[$field])) {
                unset($data[$field]);
                continue;
            }
            // 检测是否附件
            if ($component === 'xbUpload') {
                // 检测是否存在值
                if (empty($dataValue)) {
                    $data[$field] = '';
                    continue;
                }
                // 附件处理
                $path         = FileProvider::path($dataValue);
                $paths        = empty($path) ? '' : $path;
                $data[$field] = is_array($paths) ? json_encode($paths, 256) : $paths;
            }
            // 检测是否选项
            if (in_array($component, ['checkbox', 'radio', 'select']) && empty($dataValue)) {
                $data[$field] = '';
            }
        }
        // 返回数据
        return $data;
    }
}