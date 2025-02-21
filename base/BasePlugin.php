<?php
namespace plugin\xbCode\base;

use plugin\xbCode\base\plugin\UpdateTrait;
use plugin\xbCode\base\plugin\InstallTrait;
use plugin\xbCode\base\plugin\UnInstallTrait;

/**
 * 插件基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
abstract class BasePlugin
{
    // 插件安装
    use InstallTrait;
    // 插件更新
    use UpdateTrait;
    // 插件卸载
    use UnInstallTrait;

    /**
     * 安装前置
     * @param string $version 版本名称
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function installBefore(string $version = '')
    {
    }

    /**
     * 安装
     * @param string $version 版本名称
     * @param mixed $context 从<安装之前>返回的上下文
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    abstract public static function install(string $version = '', mixed $context = null);

    /**
     * 安装后置
     * @param string $version 版本名称
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function installAfter(string $version = '', mixed $context = null)
    {
        // 可以自己实现安装之后的业务逻辑...
    }

    /**
     * 更新前置
     * @param string $localVersion 本地版本
     * @param string $toVersion 更新版本
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function updateBefore(string $localVersion = '', string $toVersion = '')
    {
        // 返回数据给更新
        return [];
    }

    /**
     * 更新
     * @param string $localVersion 本地版本
     * @param string $toVersion 更新版本
     * @param mixed $context 从<安装>返回的上下文
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    abstract public static function update(string $localVersion = '', string $toVersion = '', mixed $context = null);

    /**
     * 更新后置
     * @param string $localVersion 本地版本
     * @param string $toVersion 更新版本
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function updateAfter(string $localVersion = '', string $toVersion = '', mixed $context = null)
    {
    }

    /**
     * 卸载后置
     * @param string $localVersion 本地版本
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstallBefore(string $localVersion = ''): array
    {
        // 返回数据给卸载
        return [];
    }

    /**
     * 卸载
     * @param string $localVersion 本地版本
     * @param mixed $context 从<卸载之前>返回的上下文
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    abstract public static function uninstall(string $localVersion = '', mixed $context = null);

    /**
     * 卸载后置
     * @param string $localVersion 本地版本
     * @param mixed $context 从<卸载>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstallAfter(string $localVersion = '', mixed $context = null)
    {
    }
}