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

use plugin\xbCode\api\Dict;
use plugin\xbCode\api\Menu;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\Setting;
use plugin\xbCode\api\XbCode;
use plugin\xbCode\api\Composer;

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
        // 安装定时任务（未完成）
        self::installCrontab($version, $context);
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
        $sqlShortPath = "plugin/{$name}/install.sql";
        $sqlPath = base_path($sqlShortPath);
        $sqlPath = str_replace('\\', '/', $sqlPath);
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
     *
     * setting 目录下每个文件为一个配置分组（文件名即分组标识），解析出分组模板与字段初始值，
     * 通过后台接口上报入库。后台可能独立部署，因此不直接写配置表。
     * @param string $version
     * @param array|null $context
     * @return void
     */
    private static function installConfig(string $version, array|null &$context = null)
    {
        // 安装配置数据
        $name = $context['name'] ?? '';
        if (!$name) {
            throw new \Exception('安装配置失败，插件标识错误');
        }
        $configShortPath = "plugin/{$name}/setting/*.php";
        $configPath = base_path($configShortPath);
        $configPath = str_replace('\\', '/', $configPath);
        $groups = [];
        foreach (glob($configPath) ?: [] as $file) {
            $group = Setting::parse($file);
            if ($group) {
                $groups[$group['group']] = $group;
            }
        }
        if (!$groups) {
            return;
        }
        // 后台可能独立部署，配置分组通过 HTTP 接口上报给后台写入
        Setting::install($groups, $name);
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
     *
     * 扫描插件的 enum 目录，反射每个枚举类的常量，整理成字典分组，
     * 通过后台接口上报入库。后台可能独立部署，因此不直接写字典表。
     * @param string $version
     * @param array|null $context
     * @return void
     */
    private static function installEnum(string $version, array|null &$context = null)
    {
        // 安装枚举数据
        $name = $context['name'] ?? '';
        if (!$name) {
            throw new \Exception('安装枚举失败，插件标识错误');
        }
        $enumShortPath = "plugin/{$name}/enum";
        $enumPath = base_path($enumShortPath);
        $enumPath = str_replace('\\', '/', $enumPath);
        if (!is_dir($enumPath)) {
            return;
        }
        $groups = [];
        foreach (glob($enumPath . '/*.php') ?: [] as $file) {
            $class = "plugin\\{$name}\\enum\\" . basename($file, '.php');
            // 只处理继承枚举基类的合法枚举
            if (!class_exists($class) || !is_subclass_of($class, BaseEnum::class)) {
                continue;
            }
            $items = [];
            foreach ($class::toArray() as $item) {
                $items[] = [
                    'key' => (string) ($item['key'] ?? ''),
                    'label' => (string) ($item['label'] ?? ''),
                    'value' => (string) ($item['value'] ?? ''),
                    'style' => (string) ($item['style'] ?? ''),
                ];
            }
            if (!$items) {
                continue;
            }
            $groups[] = [
                'name' => basename($file, '.php'),
                'title' => self::enumTitle($class),
                'items' => $items,
            ];
        }
        if (!$groups) {
            return;
        }
        // 后台可能独立部署，字典分组通过 HTTP 接口上报给后台写入
        Dict::install($groups, $name);
    }

    /**
     * 读取枚举标题，取类注释的首行描述
     * @param string $class 枚举类名
     * @return string
     */
    private static function enumTitle(string $class): string
    {
        $comment = (new \ReflectionClass($class))->getDocComment();
        if (is_string($comment) && $comment !== '') {
            foreach (preg_split('/\r\n|\r|\n/', $comment) ?: [] as $line) {
                $line = trim(ltrim(trim($line), '/*'));
                if ($line === '' || str_starts_with($line, '@')) {
                    continue;
                }
                return $line;
            }
        }
        return basename(str_replace('\\', '/', $class));
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
        $name = $context['name'] ?? '';
        if (!$name) {
            throw new \Exception('安装菜单失败，插件标识错误');
        }
        $menuShortPath = "plugin/{$name}/config/menu.php";
        $menuPath = base_path($menuShortPath);
        $menuPath = str_replace('\\', '/', $menuPath);
        if (!file_exists($menuPath)) {
            return;
        }
        $menus = require $menuPath;
        if (!is_array($menus) || !$menus) {
            return;
        }
        // 后台可能独立部署，菜单通过 HTTP 接口下发给后台写入
        Menu::install($menus, $name);
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
        $context = static::getContext();
        // 执行环境检测
        static::checkEnvironment();
        // 安装前
        static::beforeInstall($version, $context);
        // 安装中
        static::doInstall($version, $context);
        // 安装后
        static::afterInstall($version, $context);
        // 同步配置分组
        self::installConfig($version, $context);
        // 同步菜单
        self::installMenu($version, $context);
        // 同步字典
        self::installEnum($version, $context);
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