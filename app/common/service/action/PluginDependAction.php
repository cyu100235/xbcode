<?php
namespace app\common\service\action;

use app\common\service\CloudSerivce;
use app\common\utils\JsonUtil;

/**
 * 插件安装云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginDependAction extends PluginBaseAction
{
    use JsonUtil;

    /**
     * 递归卸载插件依赖
     * @param string $name
     * @param string $version
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checkDepend(string $plugin, string $version)
    {
        // 获取插件依赖项
        $depend = CloudSerivce::getLocalPluginDepend($plugin);
        // 依赖项检测
        if (!empty($depend)) {
            // 依赖项检测
            foreach ($depend as $name => $version) {
                $pluginDir = base_path("plugin/{$name}");
                if (is_dir($pluginDir)) {
                    $data = CloudSerivce::getLocalPluginDepend($name);
                    if (!empty($data)) {
                        self::checkDepend($name, $version);
                    }
                    // 卸载插件数据
                    parent::installData($name, $version, 'uninstall');
                    p("--- [{$plugin}]插件卸载依赖{$name}数据成功 ---");
                    // 卸载插件目录
                    remove_dir($pluginDir);
                    p("--- [{$plugin}]插件卸载依赖{$name}目录成功 ---");
                }
            }
        }
    }
}