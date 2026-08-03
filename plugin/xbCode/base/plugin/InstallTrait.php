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

use plugin\xbCode\api\PluginsApi;
use support\think\Db;
use plugin\xbCode\api\Menus;
use plugin\xbCode\api\Mysql;
use plugin\xbDict\api\DictApi;
use plugin\xbCode\api\ConfigApi;
use plugin\xbCrontab\api\CrontabApi;

/**
 * 安装插件方法
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait InstallTrait
{
    /**
     * 安装数据库
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function installSql()
    {
        // 数据库信息
        $database = Db::getConfig();
        $prefix = $database['connections']['mysql']['prefix'] ?? 'xb_';
        // 替换表前缀
        $oldPrefix = ['xb_', 'php_', '__PREFIX__'];
        $newPrefix = "{$prefix}";
        // SQL文件地址
        $sqlFile = base_path() . "/plugin/{$this->name}/install.sql";
        // 安装数据库
        Mysql::importSql($sqlFile, $oldPrefix, $newPrefix);
    }

    /**
     * 安装菜单
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function installMenus()
    {
        // 获取菜单文件
        $file = base_path() . "/plugin/{$this->name}/config/menu.php";
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
        Menus::install($data, $this->name);
    }

    /**
     * 安装字典
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function installDict()
    {
        // 检测是否安装插件
        if (!class_exists('plugin\\xbDict\\api\\DictApi')) {
            return;
        }
        // 开始安装字典数据
        DictApi::installAction()->install($this->name);
    }

    /**
     * 安装定时任务
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function installCrontab()
    {
        // 获取字典文件
        $file = base_path("/plugin/{$this->name}/config/crontab.php");
        if (!file_exists($file)) {
            return;
        }
        // 检测是否安装插件
        if (!class_exists('plugin\xbCrontab\api\CrontabApi')) {
            return;
        }
        // 检测插件是否安装
        if (!PluginsApi::make()->hasEnabled('xbCrontab')) {
            return;
        }
        // 开始安装定时任务
        CrontabApi::make()->install($this->name);
    }

    /**
     * 安装配置项
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function installConfig()
    {
        // 获取插件所有配置文件
        $files = glob(base_path("/plugin/xbUser/setting/*/*.php"));
        if (empty($files)) {
            return;
        }
        // 普通配置回调
        $configCallback = function (array $config) {
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
                $data[$value['name']] = $value['value'];
            }
            return $data;
        };
        // 选项卡配置回调
        $tabsCallback = function (array $config) use ($configCallback) {
            $data = [];
            foreach ($config as $value) {
                if (isset($value['body']) && is_array($value['body'])) {
                    $data[$value['name']] = $configCallback($value['body']);
                }
            }
            return $data;
        };
        foreach ($files as $path) {
            $config = include $path;
            if (empty($config)) {
                continue;
            }
            // 配置类型
            $type = basename(dirname($path));
            // 配置分组
            $group = basename($path, '.php');
            // 配置数据
            if ($type === 'tabs') {
                // 选项卡配置
                $data = $tabsCallback($config);
            } else {
                // 普通配置
                $data = $configCallback($config);
            }
            if (!empty($data)) {
                ConfigApi::make($group)->set($data);
            }
        }
    }
}