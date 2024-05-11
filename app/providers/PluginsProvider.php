<?php
namespace app\providers;

/**
 * 插件服务提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsProvider
{
    /**
     * 获取插件列表
     * @param string $keyword
     * @param int $page
     * @param int $limit
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getList(string $keyword = '',int $page = 1, int $limit = 20)
    {
        return [];
    }

    /**
     * 安装插件
     * @param string $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function install(string $name)
    {
    }

    /**
     * 卸载插件
     * @param string $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstall(string $name)
    {
    }
}