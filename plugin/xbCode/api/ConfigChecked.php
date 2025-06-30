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

use plugin\xbUpload\api\Files;
use plugin\xbCode\app\model\Config;

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
            if ($value && strpos($value, 'uploads/') !== false) {
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
}