<?php
namespace plugin\xbCode\api;

use plugin\xbCode\app\model\Plugins;
use plugin\xbCode\base\BasePlugin;

/**
 * 安装类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Install extends BasePlugin
{
    /**
     * 安装
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function install(string $version, mixed $context = null)
    {
        parent::install($version, $context);
    }

    /**
     * 更新
     * @param mixed $version
     * @param mixed $context
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function update(string $version, mixed $context = null)
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
    public static function uninstall(string $version, mixed $context = null)
    {
    }

    /**
     * 判断是否已经安装
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checked()
    {
        $path = base_path() . '/.env';
        if (file_exists($path)) {
            return true;
        }
        return false;
    }
}