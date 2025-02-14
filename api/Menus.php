<?php
namespace plugin\xbCode\api;

use plugin\xbCode\app\model\AdminRule;

/**
 * 菜单安装/卸载接口
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Menus
{
    /**
     * 安装菜单
     * @param string $plugin 插件标识
     * @param string $path 父级地址
     * @param array $data 菜单数据
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function install(string $plugin, string $path, array $data)
    {
        $pid = 0;
        if ($path) {
            $pid = AdminRule::where('path', $path)->value('id', $pid);
        }
    }
    
    /**
     * 卸载菜单
     * @param string $plugin 插件标识
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstall(string $plugin)
    {
        if (AdminRule::where('plugin', $plugin)->delete()) {
            return true;
        }
        return false;
    }
}