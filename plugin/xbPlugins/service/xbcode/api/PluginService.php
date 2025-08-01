<?php
namespace plugin\xbPlugins\service\xbcode\api;

use Exception;
use plugin\xbPlugins\api\PluginsApi;
use plugin\xbPlugins\service\xbcode\XbCodeServer;

/**
 * 插件服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginService extends XbCodeServer
{
    /**
     * 获取插件列表
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getPluginList()
    {
        $plugins = [];
        $service = static::request()->post('xbServerApp/api/Plugins/index',[
            'plugins' => $plugins,
        ]);
        $result = $service->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (isset($result['code']) && $result['code'] != 200) {
            throw new Exception($result['msg']);
        }
        if (!isset($result['data'])) {
            throw new Exception('获取插件列表失败');
        }
        // 获取本地已安装插件名称
        $pluginNames = PluginsApi::getInstallPluginNames();
        $data = array_map(function ($item)use($pluginNames) {
            $item['install'] = isset($pluginNames[$item['name']]) ? '20' : '10';
            $item['update'] = '10';
            return $item;
        }, $result['data']);
        return $data;
    }
}