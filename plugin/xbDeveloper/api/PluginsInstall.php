<?php
namespace plugin\xbDeveloper\api;

use plugin\xbDeveloper\base\BasePlugins;

/**
 * 插件安装接口
 * 1.执行安装脚本
 * 2.安装完成
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginsInstall extends BasePlugins
{
    /**
     * 安装完成
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function complete()
    {
        // 检测是否存在插件市场
        if (class_exists("\\plugin\\xbPlugins\\api\\PluginsInstall")) {
            $class = new \plugin\xbPlugins\api\PluginsInstall;
            $class->start('installed', $this->pluginName, $this->versionName, $this->version, true);
        }
    }
}