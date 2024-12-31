<?php
namespace xbcode\service\xbcode;

use Exception;
use support\Cache;

/**
 * 项目插件服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ProjectPluginService extends XbBaseService
{
    /**
     * 获取插件列表
     * @param array $plugins 插件标识
     * @param bool $installed 是否已安装
     * @param int $page 页码
     * @param int $limit 每页数量
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function datalist(array $plugins = [], bool $installed = false, int $page = 1, int $limit = 20)
    {
        $service = static::request()->post('ProjectsPlugin/index',[
            'plugins' => $plugins,
            'installed' => $installed ? '20' : '10',
            'page' => $page,
            'limit' => $limit
        ]);
        $result = $service->array();
        if (empty($result)) {
            return [];
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        if (!isset($result['data'])) {
            throw new Exception('插件数据错误');
        }
        return $result['data'];
    }

    /**
     * 获取项目插件详情
     * @param string $name 插件标识
     * @param string $versionName 插件版本
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function projectPluginVersion(string $name, string $versionName)
    {
        $key = "project_plugin_version_{$name}_{$versionName}";
        $result = Cache::get($key);
        if ($result) {
            return $result;
        }
        $service = static::request()->get('ProjectsPlugin/projectPluginVersion',[
            'name' => $name,
            'version_name' => $versionName,
        ]);
        $result = $service->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        if (!isset($result['data'])) {
            throw new Exception('项目插件数据错误');
        }
        // 缓存数据
        Cache::set($key, $result['data'], self::$cacheTime);
        // 返回数据
        return $result['data'];
    }
}