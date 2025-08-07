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
     * 获取插件配置
     * @param string $name 插件标识
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function config(string $name)
    {
        $path = base_path() . "/plugin/{$name}/plugins.json";
        if (!file_exists($path)) {
            throw new Exception("插件 {$name} 配置文件不存在", 1);
        }
        $content = file_get_contents($path);
        if (empty($content)) {
            throw new Exception("插件 {$name} 配置文件内容不存在", 2);
        }
        $data = json_decode($content, true);
        if (empty($data)) {
            throw new Exception("插件 {$name} 配置文件解析失败", 3);
        }
        if (!isset($data['title'])) {
            throw new Exception("插件 {$name} 配置文件缺少 title 字段", 4);
        }
        if (!isset($data['version'])) {
            throw new Exception("插件 {$name} 配置文件缺少 version 字段", 5);
        }
        if (!isset($data['author'])) {
            throw new Exception("插件 {$name} 配置文件缺少 author 字段", 6);
        }
        if (!isset($data['desc'])) {
            throw new Exception("插件 {$name} 配置文件缺少 description 字段", 7);
        }
        // 设置插件标识
        $data['name'] = $name;
        // 解析版本编号
        $str = ['.', 'v','V','-'];
        $data['version_code'] = str_replace($str, '', $data['version']);
        // 返回数据
        return $data;
    }

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