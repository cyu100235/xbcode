<?php
namespace plugin\xbUpload\api;

use plugin\xbCode\base\BasePlugin;

/**
 * 安装类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Install extends BasePlugin
{
    /**
     * 云存储引擎类型
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static $engine = 'local';

    /**
     * 安装
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function install(string $version = '', mixed $context = null)
    {
        // 安装数据库
        static::installSql();
        // 安装菜单
        static::installMenus();
        // 安装字典
        static::installDict();
        // 安装定时任务
        static::installCrontab();
        // 安装储存记录
        EngineApi::add([
            'title' => '本地存储',
            'name' => static::$engine,
            'plugin' => static::getCallPluginName(),
            'desc' => '存储在本地服务器',
            'prompt' => '本地存储方式不需要配置其他参数',
        ]);
    }

    /**
     * 更新
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function update(string $version = '', mixed $context = null)
    {
    }

    /**
     * 卸载
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstall(string $version = '', mixed $context = null)
    {
        // 插件标识
        $plugin = static::getCallPluginName();
        // 删除储存记录
        EngineApi::del($plugin, static::$engine);
    }
}