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
use plugin\xbCode\app\model\Config;

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
    public static function get(string $name = '', mixed $default = null, array $options = [])
    {
        // 是否拆分为多个查询配置
        if (str_contains($name, ',')) {
            $name = explode(',', $name);
        }
        // 查询多个配置
        if ($name && is_array($name)) {
            $data = [];
            foreach($name as $field){
                $data[$field] = static::get($field, '', $options);
            }
            return empty($data) ? $default : $data;
        }
        // 获取查询条件
        $where = static::getWhere($name);
        // 缓存KEY
        $key = static::getCacheKey($where);
        // 查询配置
        $data = Config::where($where)->cache($key, static::$expire)->column('value', 'name');
        if (empty($data)) {
            return $default;
        }
        // 是否直接获取单条数据
        if (isset($data[$name])) {
            $dataValue = $data[$name] ?? '';
            if (empty($dataValue)) {
                return $default;
            }
            // 检测是否为附件
            if (str_contains($dataValue, 'uploads/')) {
                $dataValue = Files::url($dataValue);
            }
            return $dataValue;
        }
        // 替换文件URL
        $data = ConfigChecked::replaceFileUrl($data);
        // 返回数据
        return $data;
    }

    /**
     * 获取查询条件
     * @param string $name 配置名称
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function getWhere(string &$name)
    {
        $where = [];
        if(str_contains($name, '/')) {
            $temp = explode('/', $name);
            unset($temp[0]);
            $name = implode('.', $temp);
        }
        $temp = explode('.', $name);
        $group = $temp[0] ?? '';
        $where[] = ['group', '=', $group];
        unset($temp[0]);
        $name = implode('.', $temp);
        $name = $name === '*' ? '' : $name;
        if(str_contains($name, '.*')) {
            $name = str_replace('.*', '', $name);
            $where[] = ['name', 'like', "{$name}%"];
        } else if($name){
            $where[] = ['name', '=', $name];
        }
        return $where;
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
    public static function set(string $path, array $data)
    {
        // 是否有验证器
        $validate = $data['xbValidate'] ?? null;
        if ($validate) {
            // 解析层级做验证
            $valiData = ConfigChecked::getConfigValue($data);
            xbValidate($validate, $valiData);
            unset($data['xbValidate']);
        }
        // 获取分组名称
        $group = static::getGroupName($path);
        $group = $group ?: $path;
        // 站点ID
        $saasAppid = request()->saas_appid ?? null;
        // 遍历配置数据
        foreach ($data as $field => $value) {
            if(str_contains($field, '/')) {
                $field = str_replace('/', '.', $field);
            }
            // 查询条件
            $where = [
                'saas_appid' => $saasAppid,
                'name' => $field,
            ];
            $model = Config::where($where)->find();
            if (!$model) {
                $model = new Config;
            }
            if (is_array($value)) {
                $value = empty($value) ? $value : Files::path($value);
            }
            $configData = [
                'saas_appid' => $saasAppid,
                'group' => $group,
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
     * 获取分组名称
     * @param string $name 配置名称
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getGroupName(string $name): string
    {
        // 获取分组名称
        $group = '';
        if (str_contains($name, '/')) {
            $temp = explode('/', $name);
            $group = end($temp);
            return $group;
        }
        if (str_contains($name, '.')) {
            $temp = explode('.', $name);
            $group = $temp[0] ?? '';
            return $group;
        }
        return $group;
    }

    /**
     * 获取配置名称
     * @param string $name 配置名称
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getName(string $name): string
    {
        $configName = '';
        if (str_contains($name, '/')) {
            $temp = explode('/', $name);
            unset($temp[0]);
            $configName = implode('.', $temp);
        } elseif (str_contains($name, '.')) {
            $temp = explode('.', $name);
            unset($temp[0]);
            $configName = implode('.', $temp);
        }
        return $configName;
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
        $saasAppid = request()->saas_appid ?? null;
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