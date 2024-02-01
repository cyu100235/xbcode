<?php

namespace app\common\service\cloud;

/**
 * 应用插件服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait AppPluginsCloud
{
    /**
     * 获取插件列表
     * @param string $appName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getPluginList(string $appName = '')
    {
        $params = [
            'app_name' => $appName,
        ];
        if (empty($appName)) {
            $params['app_name'] = request()->appName;
        }
        return self::send('Plugins/index', $params)->array();
    }

    /**
     * 获取插件详情
     * @param string $pluginName
     * @param string $appName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getPluginDetail(string $pluginName, string $appName = '')
    {
        $params = [
            'plugin_name'   => $pluginName,
            'app_name'      => $appName,
        ];
        if (empty($appName)) {
            $params['app_name'] = request()->appName;
        }
        return self::send('Plugins/detail', $params)->array();
    }

    /**
     * 购买插件
     * @param string $pluginName
     * @param string $appName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function buyPlugin(string $pluginName, string $appName = '')
    {
        $params = [
            'plugin_name'   => $pluginName,
            'app_name'      => $appName,
        ];
        if (empty($appName)) {
            $params['app_name'] = request()->appName;
        }
        return self::send('Orders/buyPlugin', $params)->array();
    }
    
    /**
     * 下载插件包
     * @param string $pluginName
     * @param string $version
     * @param string $package
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function downloadPlugin(string $pluginName,string $version, string $package)
    {
        $params = [
            'plugin_name'   => $pluginName,
            'version'       => $version,
        ];
        return self::send('Plugins/download', $params)->array();
    }

    /**
     * 获取已安装插件列表
     * @param string $appName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getPluginInstallList(string $appName = '')
    {
        $params = [
            'app_name' => $appName,
        ];
        return self::send('Plugins/installed', $params)->array();
    }

    /**
     * 检测插件是否安装
     * @param string $pluginName
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getPluginInstall(string $pluginName)
    {
        return false;
    }

    /**
     * 获取插件配置
     * @param string $pluginName
     * @param string $group
     * @param string $key
     * @param mixed $default
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getPluginConfig(string $pluginName, string $group = null, string $key = null, mixed $default = null)
    {
        return [];
    }
}
