<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\base\plugin;

use Exception;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\Menus;
use plugin\xbDict\api\DictApi;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\app\model\Config;
use plugin\xbCrontab\api\CrontabApi;

/**
 * 插件卸载方法
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait UnInstallTrait
{
    /**
     * 卸载数据库
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function unInstallSql()
    {
        // SQL文件地址
        $file = base_path() . "/plugin/{$this->name}/install.sql";
        if (!file_exists($file)) {
            return;
        }
        //替换的表前缀
        $prefix = ['xb_', 'php_', '__PREFIX__'];
        // 获取SQL所有表名
        $tableNames = Mysql::getSqlNames($file, $prefix);
        // 批量删除表
        Mysql::dropTables($tableNames);
    }

    /**
     * 卸载菜单
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function unInstallMenus()
    {
        // 删除菜单
        Menus::uninstall($this->name);
    }

    /**
     * 卸载字典
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function unInstallDict()
    {
        // 检测是否安装插件
        if(!class_exists(DictApi::class)){
            return;
        }
        if (!PluginsApi::make()->installed('xbDict')) {
            return;
        }
        if (!PluginsApi::make()->hasEnabled('xbDict')) {
            return;
        }
        if (!Mysql::hasTable('dict')) {
            return;
        }
        // 开始卸载字典数据
        DictApi::installAction()->uninstall($this->name);
    }

    /**
     * 卸载定时任务
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function unInstallCrontab()
    {
        // 检测插件是否已安装
        if (PluginsApi::make()->installed('xbCrontab')) {
            return;
        }
        // 检测插件是否启用
        if (!PluginsApi::make()->hasEnabled('xbCrontab')) {
            return;
        }
        // 检测表是否存在
        if (!Mysql::hasTable('crontab')) {
            return;
        }
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
        if(!class_exists(CrontabApi::class)){
            return;
        }
        // 开始卸载定时任务
        CrontabApi::make()->uninstall($plugin);
    }

    /**
     * 卸载配置项
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function unInstallConfig()
    {
        // 获取插件所有配置文件
        $files = glob(base_path() . "/plugin/{$this->name}/setting/*/*.php");
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
                if (empty($value['name'])) {
                    continue;
                }
                if (empty($value['type'])) {
                    continue;
                }
                if (!isset($value['value'])) {
                    continue;
                }
                $where = [
                    'name' => $value['name'],
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