<?php
namespace app\providers\upload;

use Shopwwi\WebmanFilesystem\Storage as BaseStorage;

class Storage
{
    /**
     * 实例
     * @var BaseStorage
     */
    protected static $_instance = null;

    /**
     * 获取实例
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function instance()
    {
        if (!static::$_instance) {
            static::$_instance = new BaseStorage(Config::getDbConfig());
        }
        return static::$_instance;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::instance()->{$name}(...$arguments);
    }
}