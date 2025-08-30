<?php
namespace plugin\xbDeveloper\api;

use plugin\xbDeveloper\base\BasePlugins;

/**
 * 插件卸载接口
 * 1.执行卸载脚本
 * 2.删除代码
 * 3.卸载完成
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsUnInstall extends BasePlugins
{
    /**
     * 卸载完成
     * @return void
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function complete()
    {
        // 检测是否存在插件市场
        if (class_exists("\\plugin\\xbPlugins\\api\\PluginsUnInstall")) {
            $class = new \plugin\xbPlugins\api\PluginsUnInstall;
            $class->start('complete', $this->pluginName, $this->versionName, $this->version, true);
        }
    }
}