<?php
namespace plugin\xbCode\base;

use plugin\xbCode\api\Composer;
use plugin\xbCode\api\Packages;
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
    public static function installBefore(string $version)
    {
        // 获取插件名称
        $name = static::getCallPluginName();
        try {
            // 检测依赖是否安装
            Packages::composer($name);
        } catch (\Throwable $th) {
            if ($th->getCode() === 4) {
                Composer::install($name);
            }
        }
        try {
            // 检查插件是否安装
            Packages::plugins($name);
        } catch (\Throwable $th) {
            if ($th->getCode() !== 1) {
                throw $th;
            }
        }
    }

    /**
     * 安装
     * @param string $version 版本名称
     * @param mixed $context 从<安装之前>返回的上下文
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    abstract public static function install(string $version, mixed $context = null);

    /**
     * 安装后置
     * @param string $version 版本名称
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function installAfter(string $version, mixed $context = null)
    {
        // 可以自己实现安装之后的业务逻辑...
    }

    /**
     * 更新前置
     * @param string $version 版本名称
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function updateBefore(string $version)
    {
        // 返回数据给更新
        return [];
    }

    /**
     * 更新
     * @param string $version 版本名称
     * @param mixed $context 从<安装>返回的上下文
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    abstract public static function update(string $version, mixed $context = null);

    /**
     * 更新后置
     * @param string $version 版本名称
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function updateAfter(string $version, mixed $context = null)
    {
    }

    /**
     * 卸载后置
     * @param string $version 版本名称
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstallBefore(string $version): array
    {
        // 返回数据给卸载
        return [];
    }

    /**
     * 卸载
     * @param string $version 版本名称
     * @param mixed $context 从<卸载之前>返回的上下文
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    abstract public static function uninstall(string $version, mixed $context = null);

    /**
     * 卸载后置
     * @param string $version 版本名称
     * @param mixed $context 从<卸载>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstallAfter(string $version, mixed $context = null)
    {
    }
}