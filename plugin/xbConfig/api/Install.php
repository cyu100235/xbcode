<?php

namespace plugin\xbConfig\api;

use plugin\xbCode\base\BasePlugin;

/**
 * 安装类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Install extends BasePlugin
{
    /**
     * 安装
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function install(string $version = '', mixed $context = null)
    {
        // 安装数据库
        static::installSql();
        // 安装菜单
        static::installMenus();
        // 安装字典
        static::installDict();
        // 安装定时任务
        static::installCrontab();
        // 安装配置项
        static::installConfig();
    }

    /**
     * 卸载
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstall(string $version = '', mixed $context = null)
    {
        // 卸载数据库
        static::unInstallSql();
        // 卸载菜单
        static::unInstallMenus();
        // 卸载字典
        static::unInstallDict();
        // 卸载定时任务
        static::unInstallCrontab();
        // 卸载配置项
        static::unInstallConfig();
    }

    /**
     * 更新
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function update(string $version = '', mixed $context = null)
    {
    }
}