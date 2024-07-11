<?php
namespace app\common\service\cloud;

use app\model\Plugins;

/**
 * 插件工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait PluginsUtil
{
    /**
     * 获取本地插件
     * @param string $state
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLocalPlugin()
    {
        $data = glob(base_path("plugin/**/info.json"));
        $list = [];
        foreach ($data as $value) {
            $item   = json_decode(file_get_contents($value), true);
            $list[] = $item;
        }
        return $list;
    }

    /**
     * 获取本地已安装插件
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLocalInstall()
    {
        $data = self::getLocalPlugin();
        foreach ($data as $key => $value) {
            $where   = [
                'name' => $value['name'],
                'state' => '20'
            ];
            $install = Plugins::where($where)->find();
            if (!$install) {
                unset($data[$key]);
            }
        }
        $data = array_values($data);
        return $data;
    }

    /**
     * 获取本地已安装插件名称
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLocalPluginName()
    {
        $plugins = self::getLocalInstall();
        if (empty($plugins)) {
            return [];
        }
        $plugins = array_column($plugins, 'name');
        return $plugins;
    }

    /**
     * 获取本地插件版本
     * @param string $name
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLocalPluginVersion(string $name)
    {
        $data = self::getLocalInstall();
        $data = array_column($data, 'version', 'name');
        return $data[$name] ?? '';
    }

    /**
     * 获取本地插件依赖
     * @param string $name
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getLocalPluginDepend(string $name)
    {
        $data = self::getLocalInstall();
        $data = array_column($data, 'depend', 'name');
        return $data[$name] ?? [];
    }

    /**
     * 检测插件是否已安装
     * @param string $name
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checkPluginInstall(string $name)
    {
        $pluginNames = self::getLocalPluginName();
        if (in_array($name, $pluginNames)) {
            return true;
        }
        return false;
    }
}