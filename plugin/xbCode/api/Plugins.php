<?php
namespace plugin\xbCode\api;

use Exception;

/**
 * 插件接口类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Plugins
{
    /**
     * 插件安装检测
     * @param mixed $name
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checked(mixed $name)
    {
        $class = "\\plugin\\{$name}\\api\\Install";
        if (!class_exists($class)) {
            return false;
        }
        return true;
    }

    /**
     * 插件安装检测并抛出异常
     * @param mixed $name
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checkedThrow(mixed $name)
    {
        if (!static::checked($name)) {
            throw new Exception("插件 {$name} 未安装");
        }
    }
}