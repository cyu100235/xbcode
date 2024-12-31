<?php

namespace xbcode\providers;

use Exception;
use support\Cache;
use think\facade\Db;
use xbcode\providers\config\ConfigAttrTrait;
use xbcode\providers\config\ConfigViewTrait;

/**
 * 配置项提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ConfigProvider
{
    // 使用配置视图
    use ConfigViewTrait;
    // 使用参数处理
    use ConfigAttrTrait;

    /**
     * 获取配置项
     * @param string $path 配置文件路径
     * @param string $name 配置名称
     * @param mixed $default 默认值
     * @param array $config 配置参数
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get(string $path, string $name = '', mixed $default = null, array $config = [])
    {
        // 分组名称
        $group = self::getGroupName($path);
        $key = "xb_config_{$group}";
        if ($name) {
            $key .= "_{$name}";
        }
        $data = Cache::get($key);
        // 返回缓存数据
        if ($data && (isset($config['refresh']) && $config['refresh'] !== true)) {
            return $data;
        }
        // 获取数据
        if (empty($name)) {
            $where = [
                'group' => $group
            ];
            $data  = Db::name('config')->where($where)->column('value', 'name');
        } else {
            $where = [
                'group' => $group,
                'name' => $name
            ];
            $data  = Db::name('config')->where($where)->value('value', []);
            if ($data) {
                $jsonData = json_decode($data, true);
                if (!is_null($jsonData)) {
                    $data = json_decode($data, true);
                }
            }
        }
        // 返回默认
        if (empty($data)) {
            return $default;
        }
        // 是否解析JSON数据
        if (isset($config['json']) && $config['json'] === true) {
            foreach ($data as $field => $value) {
                if (is_string($value)) {
                    $jsonData = json_decode($value, true);
                    if (!is_null($jsonData)) {
                        $data[$field] = $jsonData;
                    }
                }
            }
        }
        // 是否处理模板数据
        if (isset($config['template']) && $config['template'] === true) {
            // 处理模板数据
            if ($name) {
                $data = self::getTemplateData($path, $data);
            } else {
                $data = self::getTemplateData($path, $data);
            }
        }
        // 是否解析层级数据
        if (isset($config['level']) && $config['level'] === true) {
            // 解析层级
            $data = self::getConfigValue($data);
        }
        // 缓存配置数据
        Cache::set($key, $data, 3600);
        // 返回数据
        return $data;
    }

    /**
     * 设置配置项
     * @param string $path 配置文件路径
     * @param string $name 配置名称
     * @param mixed $post 配置数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function set(string $path, string $name = '',mixed $post = [])
    {
        // 分组名称
        $group = self::getGroupName($path);
        // 获取保存数据
        if (empty($post)) {
            $post = request()->post();
        }
        if (empty($group)) {
            throw new Exception('分组参数错误');
        }
        if (empty($post)) {
            throw new Exception('配置数据错误');
        }
        // 数据验证
        if (!empty($post['xbValidate'])) {
            $validate = $post['xbValidate'];
            if (!class_exists($validate)) {
                throw new Exception('验证器不存在');
            }
            xbValidate($validate, $post);
            // 删除验证器
            unset($post['xbValidate']);
        }
        // 处理模板数据
        if (is_array($post)) {
            // 模板文件路径
            $template = $name ? "{$path}_{$name}" : $path;
            // 处理模板数据
            $post = self::setTemplateData($template, $post);
        }
        // 保存配置
        if ($name) {
            // 以标识名保存
            $data = self::saveConfig($group, $name, $post);
        } else {
            // 以数组保存
            foreach ($post as $field => $value) {
                $data = self::saveConfig($group, $field, $value);
            }
        }
        // 刷新缓存
        self::get($path, $name, '', [
            'refresh' => true,
        ]);
        // 返回数据
        return $data;
    }

    /**
     * 获取分组名称
     * @param string $path 配置文件路径
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getGroupName(string $path)
    {
        $group = basename($path, '.php');
        return $group;
    }
}