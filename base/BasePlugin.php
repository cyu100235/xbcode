<?php
namespace plugin\xbCode\base;

use Exception;
use think\facade\Db;
use plugin\xbCode\api\Menus;
use plugin\xbCode\api\Mysql;
use plugin\xbDict\api\DictApi;
use plugin\xbCode\api\Packages;

/**
 * 插件基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
abstract class BasePlugin
{
    /**
     * 安装前置
     * @param string $version 版本名称
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function installBefore(string $version = '')
    {
        // 获取插件名称
        $name = self::getCallPluginName();
        try {
            // 检查依赖与插件
            Packages::checked($name);
        } catch (\Throwable $th) {
            if ($th->getCode() != 1) {
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
    public static function install(string $version = '')
    {
        // 返回数据给安装后
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
    public static function installAfter(string $version = '')
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
    public static function updateBefore(string $localVersion = '')
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
    public static function update(string $localVersion = '', string $toVersion = '')
    {
        // 返回数据给更新后
        return [];
    }

    /**
     * 更新后置
     * @param string $localVersion 本地版本
     * @param string $toVersion 更新版本
     * @param mixed $context 从<安装>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function updateAfter(string $localVersion = '', string $toVersion = '')
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
    public static function uninstall(string $localVersion = '')
    {
        // 返回数据给卸载后
        return [];
    }

    /**
     * 卸载后置
     * @param string $localVersion 本地版本
     * @param mixed $context 从<卸载>返回的上下文
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstallAfter(string $localVersion = '')
    {
    }

    /**
     * 安装数据库
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installSql()
    {
        // 数据库信息
        $database = Db::getConfig();
        $prefix = $database['connections']['mysql']['prefix'] ?? 'xb_';
        // 替换表前缀
        $oldPrefix = ['`xb_', '`php_', '`__PREFIX__'];
        $newPrefix = "`{$prefix}";
        // 获取插件名称
        $plugin = self::getCallPluginName();
        // SQL文件地址
        $sqlFile = base_path() . "/plugin/{$plugin}/install.sql";
        // 安装数据库
        Mysql::importSql($sqlFile, $oldPrefix, $newPrefix);
    }

    /**
     * 安装菜单
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installMenus()
    {
        // 获取插件名称
        $plugin = self::getCallPluginName();
        // 获取菜单文件
        $menuFile = base_path() . "/plugin/{$plugin}/config/menu.php";
        if (!file_exists($menuFile)) {
            return;
        }
        // 获取菜单数据
        $menus = include $menuFile;
        // 检测菜单数据
        if (empty($menus)) {
            return;
        }
        // 开始安装菜单
        Menus::install($menus, $plugin);
    }
    
    /**
     * 安装字典
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installDict()
    {
        // 获取插件名称
        $plugin = self::getCallPluginName();
        // 获取字典文件
        $dictFile = base_path() . "/plugin/{$plugin}/config/dict.php";
        if (!file_exists($dictFile)) {
            return;
        }
        // 获取菜单数据
        $dict = include $dictFile;
        // 检测菜单数据
        if (empty($dict)){
            return;
        }
        // 开始安装字典数据
        DictApi::installAction()->install($plugin);
    }

    /**
     * 获取调用类插件名称
     * @throws Exception
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
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