<?php

namespace xbcode\providers;

use xbcode\providers\dict\DictDataTrait;
use xbcode\providers\dict\DictTagProvider;
use xbcode\providers\dict\DictDataProvider;

/**
 * 字典提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DictProvider
{
    // 字典数据获取使用
    use DictDataTrait;

    /**
     * 数据使用实例
     * @var DictProvider
     */
    private static $_instance;

    /**
     * 标签操作实例
     * @var DictTagProvider
     */
    private static $tagInstace;

    /**
     * 标签数据操作实例
     * @var DictDataProvider
     */
    private static $dataInstace;

    /**
     * 字典标识
     * @var 
     */
    protected $name;

    /**
     * 数据使用实例
     * @return DictProvider
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function instance()
    {
        if (!static::$_instance) {
            static::$_instance = new DictProvider;
        }
        return static::$_instance;
    }

    /**
     * 设置字典标识并得到实例
     * @param string $name 字典标识，不传则获取全部字典数据
     * @return DictProvider
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get(string $name)
    {
        self::instance();
        static::$_instance->name = $name;
        return static::$_instance;
    }
    
    /**
     * 字典标签操作实例
     * @return DictTagProvider
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function tagAction()
    {
        if (!static::$tagInstace) {
            static::$tagInstace = new DictTagProvider;
        }
        return static::$tagInstace;
    }

    /**
     * 字典数据操作实例
     * @return DictDataProvider
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function dataAction()
    {
        if (!static::$dataInstace) {
            static::$dataInstace = new DictDataProvider;
        }
        return static::$dataInstace;
    }
}
