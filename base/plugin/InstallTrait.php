<?php
namespace plugin\xbCode\base\plugin;

use Exception;
use think\facade\Db;
use plugin\xbCode\api\Menus;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\Plugins;
use plugin\xbDict\api\DictApi;

/**
 * 安装插件方法
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait InstallTrait
{
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
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installDict()
    {
        // 检测是否安装插件
        Plugins::checkedThrow('xbDict');
        // 获取插件名称
        $name = self::getCallPluginName();
        // 开始安装字典数据
        DictApi::installAction()->install($name);
    }

    /**
     * 安装定时任务
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
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
        // 检测是否安装插件
        Plugins::checkedThrow('xbCrontab');
        // 获取菜单数据
        $data = include $file;
        // 检测菜单数据
        if (empty($data)){
            return;
        }
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