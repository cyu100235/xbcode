<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\base;

/**
 * 插件安装器基类
 * @copyright 贵州积木云网络科技有限公司
 * @author    楚羽幽 958416459@qq.com
 */
abstract class BasePlugin
{
    /**
     * 安装之前
     * @param string $version
     * @param array|null $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function beforeInstall(string $version, array|null &$context = null){}

    /**
     * 安装中
     * @param string $version
     * @param array|null $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function doInstall(string $version, array|null &$context = null)
    {
        // 安装composer
        self::installComposer($version, $context);
        // 安装SQL表结构
        self::installSql($version, $context);
        // 安装配置数据
        self::installConfig($version, $context);
        // 安装定时任务
        self::installCrontab($version, $context);
        // 安装枚举数据
        self::installEnum($version, $context);
        // 安装菜单数据
        self::installMenu($version, $context);
    }
    private static function installComposer(string $version, array|null &$context = null)
    {
        // 安装composer
    }
    
    private static function installSql(string $version, array|null &$context = null)
    {
        // 安装SQL表结构
    }
    private static function installConfig(string $version, array|null &$context = null)
    {
        // 安装配置数据
    }
    private static function installCrontab(string $version, array|null &$context = null)
    {
        // 安装定时任务
    }
    private static function installEnum(string $version, array|null &$context = null)
    {
        // 安装枚举数据
    }
    private static function installMenu(string $version, array|null &$context = null)
    {
        // 安装菜单数据
    }

    /**
     * 安装之后
     * @param string $version
     * @param array|null $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function afterInstall(string $version, array|null &$context = null){}

    /**
     * 安装插件
     * @param string $version
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    final public static function install(string $version)
    {
        // 上下文
        $context = null;
        // 安装前
        self::beforeInstall($version, $context);
        // 安装中
        self::doInstall($version, $context);
        // 安装后
        self::afterInstall($version, $context);
    }

    /**
     * 更新之前
     * @param string $from_version
     * @param string $to_version
     * @param array $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function beforeUpdate(string $from_version, string $to_version, array &$context){}

    /**
     * 更新中
     * @param string $from_version
     * @param string $to_version
     * @param array $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function doUpdate(string $from_version, string $to_version, array &$context){}

    /**
     * 更新之后
     * @param string $from_version
     * @param string $to_version
     * @param array $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function afterUpdate(string $from_version, string $to_version, array &$context){}

    /**
     * 更新插件
     * @param string $from_version
     * @param string $to_version
     * @param array|null $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    final public static function update(string $from_version, string $to_version, array|null $context = null)
    {
        // 更新前
        self::beforeUpdate($from_version, $to_version, $context);
        // 更新中
        self::doUpdate($from_version, $to_version, $context);
        // 更新后
        self::afterUpdate($from_version, $to_version, $context);
    }

    /**
     * 卸载之前
     * @param string $version
     * @param array $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function beforeUninstall(string $version, array &$context){}

    /**
     * 卸载中
     * @param string $version
     * @param array $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function doUninstall(string $version, array &$context)
    {
        // 卸载composer
        self::uninstallComposer($version, $context);
        // 卸载SQL表结构
        self::uninstallSql($version, $context);
        // 卸载配置数据
        self::uninstallConfig($version, $context);
        // 卸载定时任务
        self::uninstallCrontab($version, $context);
        // 卸载枚举数据
        self::uninstallEnum($version, $context);
        // 卸载菜单数据
        self::uninstallMenu($version, $context);
    }
    private static function uninstallComposer(string $version, array &$context)
    {
        // 卸载composer
    }
    private static function uninstallSql(string $version, array &$context)
    {
        // 卸载SQL表结构
    }
    private static function uninstallConfig(string $version, array &$context)
    {
        // 卸载配置数据
    }
    private static function uninstallCrontab(string $version, array &$context)
    {
        // 卸载定时任务
    }
    private static function uninstallEnum(string $version, array &$context)
    {
        // 卸载枚举数据
    }
    private static function uninstallMenu(string $version, array &$context)
    {
        // 卸载菜单数据
    }

    /**
     * 卸载之后
     * @param string $version
     * @param array $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function afterUninstall(string $version, array &$context){}

    /**
     * 卸载插件
     * @param string $version
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    final public static function uninstall(string $version)
    {
        // 卸载前
        self::beforeUninstall($version, $context);
        // 卸载中
        self::doUninstall($version, $context);
        // 卸载后
        self::afterUninstall($version, $context);
    }
}