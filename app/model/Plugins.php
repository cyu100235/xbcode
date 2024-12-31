<?php

namespace app\model;

use xbcode\Model;
use support\Cache;
use xbcode\service\xbcode\PluginService;

/**
 * 插件模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Plugins extends Model
{
    /**
     * 获取插件缓存KEY
     * @param string $name 插件标识
     * @param string $versionName 版本名称
     * @param string $version 版本编号
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getPluginCacheKey(string $name, string $versionName, string $version)
    {
        return "xb_plugin_auth_{$name}_{$versionName}_{$version}";
    }
    
    /**
     * 获取插件授权信息
     * @param string $name 插件标识
     * @param string $versionName 版本名称
     * @param string $version 版本编号
     * @param bool $force 是否强制获取
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getAuthData(string $name, string $versionName, string $version, bool $force = false)
    {
        // 缓存授权信息名称
        $key    = $this->getPluginCacheKey($name,$versionName,$version);
        $result = Cache::get($key);
        if ($result && !$force) {
            return $result;
        }
        $result = PluginService::authorize($name, $versionName, $version);
        if (empty($result)) {
            throw new \Exception('获取插件授权失败');
        }
        // 缓存授权缓存
        Cache::set($key, $result, 600);
        // 刷新本地缓存
        $this->pluginCacheDict(true);
        // 返回数据
        return $result;
    }

    /**
     * 获取已安装插件字典
     * @param string $fields
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function pluginDict(string $fields = '*')
    {
        return $this->column($fields, 'name');
    }
    
    /**
     * 获取已安装插件缓存字典
     * @param bool $force
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function pluginCacheDict(bool $force = false)
    {
        $key = 'xb_installed_plugins_dict';
        $data    = Cache::get($key);
        if ($data && !$force) {
            return $data;
        }
        $plugins = $this->pluginDict();
        // 设置插件缓存信息
        Cache::set($key, $plugins, 600);
        // 返回数据
        return $plugins;
    }

    /**
     * 保存插件安装记录
     * @param string $name 插件标识
     * @param string $versionName 版本标识
     * @param int $version 版本编号
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pluginInstall(string $name, string $versionName, int $version)
    {
        // 获取插件授权信息
        $result = PluginService::authorize($name, $versionName, $version);
        // 保存插件信息
        $model  = self::where('name', $result['plugin_name'])->find();
        if (!$model) {
            $model = new self;
        }
        $data = [
            'title' => $result['plugin_title'],
            'name' => $result['plugin_name'],
            'version_name' => $result['version_name'],
            'version' => $version,
            'auth_key' => $result['auth_key'],
            'state' => '10',
        ];
        if (!$model->save($data)) {
            throw new \Exception('插件安装失败');
        }
        return $model;
    }
    
    /**
     * 插件更新完成
     * @param string $name 插件标识
     * @param string $versionName 版本标识
     * @param int $version 版本编号
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pluginUpdate(string $name, string $versionName, int $version)
    {
        // 获取插件授权信息
        $result = PluginService::authorize($name, $versionName, $version);
        // 保存插件信息
        $model  = self::where('name', $result['plugin_name'])->find();
        if (!$model) {
            throw new \Exception('该插件未安装');
        }
        $data = [
            'version_name' => $result['version_name'],
            'version' => $version,
        ];
        if (!$model->save($data)) {
            throw new \Exception('本地插件更新失败');
        }
        return $model;
    }
}
