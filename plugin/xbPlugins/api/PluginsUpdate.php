<?php
namespace plugin\xbPlugins\api;

use plugin\xbPlugins\base\BasePlugins;

/**
 * 插件安装接口
 * 1.下载插件
 * 2.备份插件代码
 * 3.备份插件数据
 * 4.解压插件包
 * 5.执行更新脚本
 * 6.更新完成
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsUpdate extends BasePlugins
{
    /**
     * 卸载完成
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function complete()
    {
    }
}