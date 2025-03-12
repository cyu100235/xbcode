<?php

namespace plugin\xbConfig\api;

use Exception;
use plugin\xbUpload\api\Files;
use plugin\xbConfig\app\model\Config;

/**
 * 配置数据处理接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ConfigChecked
{
    /**
     * 保存配置
     * @param string $group
     * @param string $name
     * @param mixed $value
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function saveConfig(string $group, string $name, mixed $value)
    {
        if (is_array($value)) {
            $value = json_encode($value, 256);
        }
        $saasAppid = request()->saasAppid ?? null;
        $where = [
            'group' => $group,
            'name' => $name,
            'saas_appid' => $saasAppid
        ];
        $data  = Config::where($where)->find();
        if (empty($data)) {
            $model = new Config;
            $model->save([
                'saas_appid' => $saasAppid,
                'group' => $group,
                'name' => $name,
                'value' => $value
            ]);
        } else {
            Config::where($where)->save(['value' => $value]);
        }
        return $value;
    }

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
            $field = str_replace($name,'', $field);
            if (empty($field)) {
                continue;
            }
            $list[$field] = $value;
        }
        return $list;
    }

    /**
     * 解析配置项层级
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
     * @copyright 贵州积木云网络科技有限公司
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getTemplateData(string $path, array $data)
    {
        $template = ConfigView::getConfigTemplate($path);
        if (empty($template)) {
            throw new Exception('配置模板规则数据错误');
        }
        // 处理数据
        foreach ($template as $value) {
            // 拦截无需验证的组件
            if (in_array($value['type'], ['xbTitle', 'xbAlert', 'NDivider'])) {
                continue;
            }
            // 获取字段名称
            $field = $value['field'] ?? '';
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
                $filePath = empty($dataValue) ? $value['value'] : $dataValue;
                $url      = Files::url($filePath);
                if (is_array($url)) {
                    $url = count($url) === 1 ? current($url) : $url;
                }
                $data[$field] = $url;
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function setTemplateData(string $path, array $data)
    {
        // 获取模板规则
        $template = ConfigView::getConfigTemplate($path);
        if (empty($template)) {
            throw new Exception('配置模板规则数据错误');
        }
        // 处理数据
        foreach ($template as $value) {
            if (in_array($value['type'], ['xbTitle', 'xbAlert', 'NDivider'])) {
                continue;
            }
            // 获取字段名称
            $field = $value['field'] ?? '';
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
                $path         = Files::path($dataValue);
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