<?php
namespace plugin\xbPlugins\api;

use plugin\xbPlugins\base\BasePlugins;

/**
 * 插件安装接口
 * 1.下载插件
 * 2.解压更新包
 * 3.执行安装脚本
 * 4.更新完成
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsInstall extends BasePlugins
{
    /**
     * 安装完成
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function complete()
    {
        // 安装完成，新增插件记录
        $this->installed();
    }
}