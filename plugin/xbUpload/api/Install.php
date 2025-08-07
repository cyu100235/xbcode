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
        parent::install($version, $context);
        // 安装储存记录
        EngineApi::init(static::$engine);
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
        parent::uninstall($version, $context);
        // 插件标识
        $plugin = static::getCallPluginName();
        // 删除储存记录
        EngineApi::del($plugin, static::$engine);
    }
}