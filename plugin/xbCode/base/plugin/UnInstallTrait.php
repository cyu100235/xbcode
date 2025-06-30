<?php
namespace plugin\xbCode\base\plugin;

use Exception;
use think\facade\Db;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\Menus;
use plugin\xbDict\api\DictApi;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCrontab\api\CrontabApi;
use plugin\xbCode\app\model\Config;

/**
 * 插件卸载方法
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait UnInstallTrait
{
    /**
     * 卸载数据库
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function unInstallSql()
    {
        // 获取插件名称
        $name = self::getCallPluginName();
        // SQL文件地址
        $file = base_path() . "/plugin/{$name}/install.sql";
        if (!file_exists($file)) {
            return;
        }
        // 获取文件内容
        $data = file_get_contents($file);
        if (empty($data)) {
            return;
        }
        $sql = explode(';', $data);
        $prefix = config('plugin.xbCode.thinkorm.connections.mysql.prefix');
        if (empty($prefix)) {
            throw new Exception('获取数据库前缀错误');
        }
        $str1 = ['`xb_', '`php_', '`__PREFIX__'];
        foreach ($sql as $value) {
            // 检测是否是创建表语句
            if (strrpos($value, 'CREATE TABLE') === false) {
                continue;
            }
            // 匹配表名
            if (!preg_match('/CREATE TABLE (.*)`/', $value, $match)) {
                continue;
            }
            // 获取完整表名
            $fullTableName = $match[1] ?? '';
            if (empty($fullTableName)) {
                continue;
            }
            // 替换表前缀
            $tableName = str_replace($str1, '', $fullTableName);
            // 检测表不存在
            if (!Mysql::hasTable($tableName)) {
                continue;
            }
            // 替换完整表前缀
            $fullTableName = str_replace($str1, "{$prefix}", $fullTableName);
            // 删除表
            $sql = "DROP TABLE IF EXISTS {$fullTableName}";
            // 执行SQL语句
            Db::execute($sql);
        }
    }

    /**
     * 卸载菜单
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function unInstallMenus()
    {
        // 获取插件名称
        $name = self::getCallPluginName();
        // 删除菜单
        Menus::uninstall($name);
    }

    /**
     * 卸载字典
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function unInstallDict()
    {
        // 检测是否安装插件
        PluginsApi::checkedThrow('xbDict');
        // 获取插件名称
        $name = self::getCallPluginName();
        // 开始卸载字典数据
        DictApi::installAction()->uninstall($name);
    }

    /**
     * 卸载定时任务
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function unInstallCrontab()
    {
        // 获取插件名称
        $plugin = self::getCallPluginName();
        // 获取字典文件
        $file = base_path() . "/plugin/{$plugin}/config/crontab.php";
        if (!file_exists($file)) {
            return;
        }
        // 检测是否安装插件
        PluginsApi::checkedThrow('xbCrontab');
        // 获取菜单数据
        $data = include $file;
        // 检测菜单数据
        if (empty($data)){
            return;
        }
        // 开始卸载定时任务
        CrontabApi::uninstall($data, $plugin);
    }

    /**
     * 卸载配置项
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function unInstallConfig()
    {
        // 获取插件名称
        $plugin = self::getCallPluginName();
        // 获取插件所有配置文件
        $files = glob(base_path() . "/plugin/{$plugin}/setting/*.php");
        if (empty($files)) {
            return;
        }
        // 检测表是否存在
        if (!Mysql::hasTable('config')) {
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
                $where = [
                    'name' => $value['field'],
                ];
                $model = Config::where($where)->find();
                if (empty($model)) {
                    continue;
                }
                if (!$model->delete()) {
                    throw new Exception('配置项删除失败');
                }
            }
        }
    }
}