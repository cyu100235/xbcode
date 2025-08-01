<?php
namespace plugin\xbPlugins\api;

use plugin\xbCode\utils\DirUtil;
use plugin\xbPlugins\app\model\Plugins;
use plugin\xbPlugins\base\BasePlugins;

/**
 * 插件卸载接口
 * 1.执行卸载脚本
 * 2.删除代码
 * 3.卸载完成
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsUnInstall extends BasePlugins
{
    /**
     * 删除代码
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function delCode()
    {
        $pluginPath = $this->pluginPath;
        if (!is_dir($pluginPath)) {
            return;
        }
        DirUtil::delDir($pluginPath);
    }

    /**
     * 卸载完成
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function complete()
    {
        // 删除插件记录
        Plugins::where('name', $this->pluginName)->delete();
    }
}