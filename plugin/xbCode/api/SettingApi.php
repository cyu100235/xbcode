<?php
namespace plugin\xbCode\api;

/**
 * 插件数据配置项
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class SettingApi
{
    /**
     * 获取插件配置项
     * @param string $name 插件名称
     * @param string $dir 目录名称
     * @param string $file 文件名称
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function get(string $name, string $dir, string $file = 'config')
    {
        $path = base_path() . "/plugin/{$name}/setting/{$dir}/{$file}.php";
        if(!file_exists($path)) {
            return [];
        }
        $config = include $path;
        if (!is_array($config)) {
            return [];
        }
        return $config;
    }
}