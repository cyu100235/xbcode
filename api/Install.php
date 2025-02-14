<?php
namespace plugin\xbCode\api;

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
     * @param mixed $version
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function install($version = '')
    {
        // 导入数据库
        static::installSql();
    }

    /**
     * 更新
     * @param mixed $localVersion
     * @param mixed $toVersion
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function update($localVersion = '', $toVersion = '')
    {
    }

    /**
     * 卸载
     * @param mixed $version
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstall($version = '')
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