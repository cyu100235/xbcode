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
        // 安装业务逻辑
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
    protected static function doUninstall(string $version, array &$context){}

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