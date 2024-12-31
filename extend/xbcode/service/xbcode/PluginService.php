<?php
namespace xbcode\service\xbcode;

use Exception;
use support\Cache;
use app\model\Plugins;

/**
 * 插件服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginService extends XbBaseService
{
    /**
     * 获取插件授权信息
     * @param string $name
     * @param string $versionName
     * @param int $version
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function authorize(string $name, string $versionName, int $version)
    {
        $service = static::request()->get('Plugins/authorize',[
            'name' => $name,
            'version_name' => $versionName,
            'version' => $version
        ]);
        $result = $service->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        if (empty($result['data'])) {
            throw new Exception('插件授权信息不存在');
        }
        return $result['data'];
    }
    
    /**
     * 检查版本更新
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checked()
    {
        // 获取插件列表
        $plugins = new Plugins;
        $pluginDict = $plugins->pluginDict();
        $pluginNames = array_map(function($item){
            return "{$item['name']}_{$item['version']}";
        }, $pluginDict);
        $key = implode('_', $pluginNames);
        $result = Cache::get("xb_plugins_version_{$key}");
        if ($result) {
            return $result;
        }        
        // 检测插件版本
        $service = static::request()->post('Plugins/checked',[
            'plugins' => $pluginDict,
        ]);
        $result = $service->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        if (!isset($result['data'])) {
            throw new Exception('获取插件版本更新信息失败');
        }
        // 缓存插件版本信息
        Cache::set("xb_plugins_version_{$key}", $result['data'], self::$cacheTime);
        // 返回数据
        return $result['data'];
    }

    /**
     * 下载插件
     * @param string $name 插件标识
     * @param string $versionName 版本名称
     * @param int $version 版本编号
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function download(string $name,string $versionName, int $version)
    {
        $service = static::request()->get('Plugins/download',[
            'name' => $name,
            'version_name' => $versionName,
            'version' => $version
        ]);
        $content = $service->body();
        $result = $service->array();
        if (empty($content) && empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        return $content;
    }
}