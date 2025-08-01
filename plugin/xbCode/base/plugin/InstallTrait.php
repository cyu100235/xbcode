<?php
namespace plugin\xbCode\base\plugin;

use think\facade\Db;
use plugin\xbCode\api\Menus;
use plugin\xbCode\api\Mysql;
use plugin\xbDict\api\DictApi;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCrontab\api\CrontabApi;

/**
 * 安装插件方法
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait InstallTrait
{
    /**
     * 安装数据库
     * @return void
     * @copyright 贵州积木云网络科技有限公司
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installMenus()
    {
        // 获取插件名称
        $name = self::getCallPluginName();
        // 获取菜单文件
        $file = base_path() . "/plugin/{$name}/config/menu.php";
        if (!file_exists($file)) {
            return;
        }
        // 获取菜单数据
        $data = include $file;
        // 检测菜单数据
        if (empty($data)) {
            return;
        }
        // 开始安装菜单
        Menus::install($data, $name);
    }
    
    /**
     * 安装字典
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installDict()
    {
        // 检测是否安装插件
        if (!class_exists('plugin\xbDict\api\DictApi')) {
            return;
        }
        // 获取插件名称
        $name = self::getCallPluginName();
        // 开始安装字典数据
        DictApi::installAction()->install($name);
    }

    /**
     * 安装定时任务
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installCrontab()
    {
        // 获取插件名称
        $plugin = self::getCallPluginName();
        // 获取字典文件
        $file = base_path() . "/plugin/{$plugin}/config/crontab.php";
        if (!file_exists($file)) {
            return;
        }
        // 获取菜单数据
        $data = include $file;
        // 检测菜单数据
        if (empty($data)){
            return;
        }
        // 检测是否安装插件
        if (!class_exists('plugin\xbCrontab\api\CrontabApi')) {
            return;
        }
        // 开始安装定时任务
        CrontabApi::install($data, $plugin);
    }

    /**
     * 安装配置项
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installConfig()
    {
        // 获取插件名称
        $plugin = self::getCallPluginName();
        // 获取插件所有配置文件
        $files = glob(base_path() . "/plugin/{$plugin}/setting/*.php");
        if (empty($files)) {
            return;
        }
        foreach ($files as $path) {
            $config = include $path;
            if (empty($config)) {
                continue;
            }
            $group = basename($path, '.php');
            $data = [];
            foreach ($config as $value) {
                if (empty($value['field'])) {
                    continue;
                }
                if (empty($value['type'])) {
                    continue;
                }
                if (!isset($value['value'])) {
                    continue;
                }
                if ($value['field'] === 'config' && $value['type'] === 'type') {
                    continue;
                }
                if ($value['type'] === 'xbTitle') {
                    continue;
                }
                $field = "{$group}.{$value['field']}";
                $data[$field] = $value['value'];
            }
            ConfigApi::set($group,$data);
        }
    }
}