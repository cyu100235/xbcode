<?php
namespace plugin\xbCode\api;

use Exception;

/**
 * 依赖包检测
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Packages
{
    /**
     * 检查依赖包
     * @param string $plugin 插件标识
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checked(string $plugin)
    {
        self::composer($plugin);
        self::plugins($plugin);
    }

    /**
     * 检查Composer依赖包
     * @param string $plugin 插件标识
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function composer(string $plugin)
    {
        $data = self::getPackages($plugin, 'composer');
        if (empty($data)) {
            return;
        }
        foreach ($data as $class => $value) {
            if (!class_exists($class)) {
                throw new Exception("请先安装 {$value} 依赖", 4);
            }
        }
    }

    /**
     * 检查插件依赖包
     * @param string $plugin 插件标识
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function plugins(string $plugin)
    {
        $data = self::getPackages($plugin, 'plugins');
        if (empty($data)) {
            return;
        }
        foreach ($data as $name => $value) {
            $class = "plugin\\{$name}\\api\\Install";
            if (!class_exists($class)) {
                throw new Exception("请先安装 {$value} 插件", 4);
            }
        }
    }

    /**
     * 获取依赖包数据
     * @param string $plugin 插件标识
     * @param string $type 数据类型
     * @throws \Exception
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getPackages(string $plugin, string $type)
    {
        $composerPath = base_path() . "/plugin/{$plugin}/plugins.json";
        if (!file_exists($composerPath)) {
            throw new Exception("插件 {$plugin} 依赖文件不存在", 1);
        }
        $content = file_get_contents($composerPath);
        if (empty($content)) {
            throw new Exception("插件 {$plugin} 依赖文件内容不存在", 2);
        }
        $data = json_decode($content, true);
        if (!isset($data[$type])) {
            throw new Exception("插件 {$plugin} 解析依赖 {$type} 类型失败", 3);
        }
        return $data[$type];
    }
}