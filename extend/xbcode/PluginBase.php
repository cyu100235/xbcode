<?php
namespace xbcode;

/**
 * 插件基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
abstract class PluginBase
{
    /**
     * 安装之前
     * @param string $version 版本名称
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installBefore(string $version): array
    {
        // 可以自己实现安装之前的业务逻辑...
        return [];
    }

    /**
     * 安装
     * @param string $version 版本名称
     * @param mixed $context 从<安装之前>返回的上下文
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(string $version, mixed $context): array
    {
        // 返回数据给安装后
        return [];
    }

    /**
     * 安装之后
     * @param string $version 版本名称
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function installAfter(string $version, mixed $context)
    {
        // 可以自己实现安装之后的业务逻辑...
    }

    /**
     * 更新前
     * @param string $localVersion 本地版本
     * @param string $toVersion 更新版本
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateBefore(string $localVersion, string $toVersion): array
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
    public function update(string $localVersion, string $toVersion, mixed $context)
    {
        // 返回数据给更新后
        return [];
    }

    /**
     * 更新后
     * @param string $localVersion 本地版本
     * @param string $toVersion 更新版本
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function updateAfter(string $localVersion, string $toVersion, mixed $context)
    {
    }

    /**
     * 卸载之前
     * @param string $localVersion 本地版本
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallBefore(string $localVersion): array
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
    public function uninstall(string $localVersion, mixed $context): array
    {
        // 返回数据给卸载后
        return [];
    }

    /**
     * 卸载后
     * @param string $localVersion 本地版本
     * @param mixed $context 从<卸载>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstallAfter(string $localVersion, mixed $context)
    {
    }
}