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

use plugin\xbCode\api\Composer;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\XbCode;

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
    protected static function beforeInstall(string $version, array|null &$context = null)
    {
    }

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
        Composer::install($context['composer'] ?? []);
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

    /**
     * 安装SQL表结构
     * @param string $version
     * @param array|null $context
     * @return void
     */
    private static function installSql(string $version, array|null &$context = null)
    {
        // 安装SQL表结构
        $name = $context['name'] ?? '';
        if (!$name) {
            throw new \Exception('安装SQL失败，插件标识错误');
        }
        $sqlPath = str_replace('\\', '/', base_path("plugin/{$name}/install.sql"));
        if (!file_exists($sqlPath)) {
            return;
        }
        if (!filesize($sqlPath)) {
            return;
        }
        // 导入SQL文件
        Mysql::importSql($sqlPath);
    }

    /**
     * 安装配置数据
     * @param string $version
     * @param array|null $context
     * @return void
     */
    private static function installConfig(string $version, array|null &$context = null)
    {
        // 安装配置数据
    }

    /**
     * 安装定时任务
     * @param string $version
     * @param array|null $context
     * @return void
     */
    private static function installCrontab(string $version, array|null &$context = null)
    {
        // 安装定时任务
    }

    /**
     * 安装枚举数据
     * @param string $version
     * @param array|null $context
     * @return void
     */
    private static function installEnum(string $version, array|null &$context = null)
    {
        // 安装枚举数据
    }

    /**
     * 安装菜单数据
     * @param string $version
     * @param array|null $context
     * @return void
     */
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
    protected static function afterInstall(string $version, array|null &$context = null)
    {
    }

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
        $context = self::getContext();
        // 执行环境检测
        self::checkEnvironment();
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
    protected static function beforeUpdate(string $from_version, string $to_version, array &$context)
    {
    }

    /**
     * 更新中
     * @param string $from_version
     * @param string $to_version
     * @param array $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function doUpdate(string $from_version, string $to_version, array &$context)
    {
        // 更新composer依赖
        self::updateComposer($from_version, $to_version, $context);
    }

    /**
     * 更新Composer
     * @param string $from_version
     * @param string $to_version
     * @param array $context
     * @return void
     */
    private static function updateComposer(string $from_version, string $to_version, array &$context)
    {
        // 更新composer依赖
        Composer::update($context['composer'] ?? []);
    }

    /**
     * 更新之后
     * @param string $from_version
     * @param string $to_version
     * @param array $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function afterUpdate(string $from_version, string $to_version, array &$context)
    {
    }

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
        // 上下文
        $context = self::getContext($context);
        // 执行环境检测
        self::checkEnvironment();
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
    protected static function beforeUninstall(string $version, array &$context)
    {
    }

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
        Composer::uninstall($context['composer'] ?? [], $context['name'] ?? '');
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

    /**
     * 卸载SQL表结构
     * @param string $version
     * @param array $context
     * @return void
     */
    private static function uninstallSql(string $version, array &$context)
    {
        // 卸载SQL表结构
    }

    /**
     * 卸载配置数据
     * @param string $version
     * @param array $context
     * @return void
     */
    private static function uninstallConfig(string $version, array &$context)
    {
        // 卸载配置数据
    }

    /**
     * 卸载定时任务
     * @param string $version
     * @param array $context
     * @return void
     */
    private static function uninstallCrontab(string $version, array &$context)
    {
        // 卸载定时任务
    }

    /**
     * 卸载枚举数据
     * @param string $version
     * @param array $context
     * @return void
     */
    private static function uninstallEnum(string $version, array &$context)
    {
        // 卸载枚举数据
    }

    /**
     * 卸载菜单数据
     * @param string $version
     * @param array $context
     * @return void
     */
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
    protected static function afterUninstall(string $version, array &$context)
    {
    }

    /**
     * 卸载插件
     * @param string $version
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    final public static function uninstall(string $version, array|null $context = null)
    {
        // 上下文
        $context = self::getContext($context);
        // 执行环境检测
        self::checkEnvironment();
        // 卸载前
        self::beforeUninstall($version, $context);
        // 卸载中
        self::doUninstall($version, $context);
        // 卸载后
        self::afterUninstall($version, $context);
    }

    /**
     * 执行环境检测
     * @return void
     */
    final public static function checkEnvironment()
    {
        // 安装检测
        XbCode::checkEnv();
        // 检测数据库连接是否正常
        XbCode::checkDatabase();
    }

    /**
     * 获取上下文
     * @param array $context
     * @return array
     */
    final public static function getContext(array &$context = null)
    {
        // 插件目录名称
        $pluginName = self::getPluginName();
        // 插件配置文件路径
        $configPath = base_path('plugin' . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . 'plugin.json');
        if (!file_exists($configPath)) {
            throw new \Exception('插件配置文件不存在：' . $configPath);
        }
        // 解析插件配置
        $config = json_decode((string) file_get_contents($configPath), true);
        if (!is_array($config)) {
            throw new \Exception('插件配置文件格式错误：' . $configPath);
        }
        // 合并调用方传入的上下文
        $context = array_merge(['name' => $pluginName], $config, $context ?? []);
        return $context;
    }

    /**
     * 获取插件目录名称
     * @return string
     */
    final protected static function getPluginName(): string
    {
        // 调用方命名空间，如 plugin\xbAdmin\api\Install
        $namespace = (new \ReflectionClass(get_called_class()))->getNamespaceName();
        $segments = explode('\\', $namespace);
        if (($segments[0] ?? '') !== 'plugin' || empty($segments[1])) {
            throw new \Exception('无法解析插件目录名称：' . $namespace);
        }
        return $segments[1];
    }
}