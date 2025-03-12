<?php

namespace plugin\xbConfig\api;

use Exception;
use plugin\xbConfig\app\model\Config;

/**
 * 配置接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ConfigApi
{
    /**
     * 缓存时间
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $expire = 3600;
    
    /**
     * 获取配置项
     * @param string $name 配置键名
     * @param mixed $default 默认值
     * @param array $option 配置项
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function get(string $name = null, mixed $default = null, array $options = [])
    {
        // 是否处理层级
        $layer = $options['layer'] ?? true;
        // 是否替换键名
        $replace = $options['replace'] ?? true;
        // 站点ID
        $saasAppid = request()->saasAppid ?? null;
        // 查询条件
        $where = [
            ['saas_appid', '=', $saasAppid],
        ];
        // 替换参数
        if ($name && strrpos($name, '/') !== false) {
            $temp  = explode('/', $name);
            unset($temp[0]);
            $name = implode('/', $temp);
            $name = str_replace('/', '.', $name);
        }
        // 检测存在逗号
        if ($name && strrpos($name,",") !== false) {
            $name = explode(",", $name);
        }
        // 字符串查询
        if ($name && is_string($name)) {
            // 检测不存在点号
            if (strpos($name, '.') === false) {
                $name = "{$name}.";
            }
            $where[] = ['name', 'like', "{$name}%"];
        }
        // 数组则完整查询
        if ($name && is_array($name)) {
            $where[] = ['name', 'in', $name];
        }
        // 缓存KEY
        $key = static::getCacheKey($where);
        // 查询配置
        $data = Config::where($where)->cache($key, static::$expire)->column('value', 'name');
        if (empty($data)) {
            return $default;
        }
        // 是否直接获取单条数据
        if (is_string($name) && isset($data[$name])) {
            return $data[$name];
        }
        // 替换配置键名
        if ($replace) {
            $data = ConfigChecked::replaceKeys($name, $data);
        }
        // 是否处理层级解析
        if ($layer) {
            // 解析层级数据
            $data = ConfigChecked::getConfigValue($data);
        }
        // 返回数据
        return $data;
    }

    /**
     * 保存配置项
     * @param string $path 配置路径
     * @param mixed $data 配置数据
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function set(array $data)
    {
        // 是否有验证器
        $validate = $data['xbValidate'] ?? null;
        if ($validate) {
            // 解析层级做验证
            $valiData = ConfigChecked::getConfigValue($data);
            xbValidate($validate, $valiData);
            unset($data['xbValidate']);
        }
        // 站点ID
        $saasAppid = request()->saasAppid ?? null;
        // 遍历配置数据
        foreach ($data as $field => $value) {
            // 替换键名称
            $field = str_replace('/', '.', $field);
            // 查询条件
            $where = [
                'saas_appid' => $saasAppid,
                'name' => $field,
            ];
            $model = Config::where($where)->find();
            if (!$model) {
                $model = new Config;
            }
            $configData = [
                'saas_appid' => $saasAppid,
                'name' => $field,
                'value' => $value,
            ];
            if (!$model->save($configData)) {
                throw new Exception('配置保存失败');
            }
            // 刷新缓存
            static::refresh($field);
        }
    }
    
    /**
     * 刷新缓存
     * @param string $name 配置名称
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function refresh(string $name = null)
    {
        // 站点ID
        $saasAppid = request()->saasAppid ?? null;
        // 查询条件
        $where = [
            ['saas_appid', '=', $saasAppid],
        ];
        if ($name) {
            $where[] = ['name', 'like', "%{$name}%"];
        }
        // 缓存KEY
        $key = static::getCacheKey($where);
        // 查询强制刷新缓存
        Config::where($where)->cacheForce($key, static::$expire)->column('value', 'name');
    }
    
    /**
     * 获取缓存KEY
     * @param array $condition
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function getCacheKey(array $condition)
    {
        $key = json_encode($condition, 256);
        $key = md5($key);
        $key = "xb_config_{$key}";
        return $key;
    }
}