<?php

namespace app\common\service\cloud;

/**
 * 应用插件服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait AppPluginsCloud
{
    // 获取插件列表
    public static function getPluginList()
    {
        return self::send('AppPlugins/index');
    }
    // 获取插件详情
    public static function getPluginInfo()
    {
        return self::send('AppPlugins/getPluginsInfo');
    }
    // 购买插件
    public static function createPluginOrder()
    {
        return self::send('Order/createPluginOrder');
    }
    // 下载插件包
    public static function downloadPlugin()
    {
        return self::send('AppPlugins/downloadPlugin');
    }
}
