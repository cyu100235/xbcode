<?php
namespace plugin\xbCode\base;

use Exception;
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
     * 执行对应的安装方法
     * @param string $method
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function installMethod(string $method)
    {
        // 方法名首字母转大写
        $method = ucfirst($method);
        // 方法名称
        $methodName = "install{$method}";
        if (!method_exists(static::class, $methodName)) {
            throw new Exception('执行安装方法错误');
        }
        return call_user_func([static::class, $methodName]);
    }

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
    public static function install(string $version, mixed $context = null)
    {
        try {
            // 安装数据库
            static::installSql();
            // 安装配置
            static::installConfig();
            // 安装菜单
            static::installMenus();
            // 安装字典
            static::installDict();
            // 安装定时任务
            static::installCrontab();
        } catch (\Throwable $th) {
            static::uninstall($version, $context);
            throw $th;
        }
        // 返回数据给安装
        return [];
    }

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
    public static function uninstall(string $version, mixed $context = null)
    {
        // 删除数据库
        static::uninstallSql();
        // 删除配置
        static::uninstallConfig();
        // 删除菜单
        static::uninstallMenus();
        // 删除字典
        static::uninstallDict();
        // 删除定时任务
        static::uninstallCrontab();
        // 返回数据给卸载
        return [];
    }

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
    
    /**
     * 获取调用类插件名称
     * @throws Exception
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getCallPluginName()
    {
        // 获取调用类命名空间
        $callClass = get_called_class();
        // 获取插件名称
        $parseClass = explode('\\', $callClass);
        // 检测是否正确获取到插件名称
        if (empty($parseClass[1]) || $parseClass[0] != 'plugin') {
            throw new Exception('插件名称获取失败');
        }
        // 返回插件名称
        return $parseClass[1];
    }
}