<?php

namespace app\model;

use xbcode\Model;
use support\Cache;
use xbcode\service\xbcode\UserService;

/**
 * 站点插件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebPlugin extends Model
{
    /**
     * 获取本地全部插件
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getLocalPluginAll()
    {
        $data = [];
        $path = base_path() . '/plugin/*';
        $list = glob($path, GLOB_ONLYDIR);
        foreach ($list as $key => $pluginPath) {
            $name = basename($pluginPath);
            $data[] = [
                'name' => $name,
                'title' => $name,
                'expire_time' => '',
            ];
        }
        return $data;
    }

    /**
     * 获取站点全部授权插件
     * @param bool $force 是否强制刷新
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getWebAuthPluginAll(bool $force = false)
    {
        $key  = 'xb_web_auth_plugin';
        $data = Cache::get($key);
        if ($data && !$force) {
            return $data;
        }
        // 获取授权插件
        $where = [
            'plugins.state' => '20',
        ];
        $data  = self::alias('site')
            ->join('plugins', 'plugins.name=site.name')
            ->where($where)
            ->field('site.*')
            ->select()
            ->toArray();
        // 设置授权插件缓存
        Cache::set($key, $data, 600);
        // 返回数据
        return $data;
    }

    /**
     * 获取站点生效中授权插件
     * @param bool $force
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getWebAuthPlugin(bool $force = false)
    {
        $data   = self::getWebAuthPluginAll($force);
        $result = [];
        foreach ($data as $item) {
            $expireTime = $item['expire_time'];
            if (empty($expireTime)) {
                $result[] = $item;
                continue;
            }
            // 判断是否过期
            $expireTime = strtotime($expireTime);
            if ($expireTime > time()) {
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * 获取插件工作台路由
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getWorkbenchRoute()
    {
        $data   = self::getWebAuthPlugin();
        $result = [];
        foreach ($data as $item) {
            $class = "\\plugin\\{$item['name']}\\app\\controller\\IndexController";
            if (!class_exists($class)) {
                continue;
            }
            $controller = new $class();
            if (!method_exists($controller, 'workbench')) {
                continue;
            }
            $result[$item['name']] = "app/{$item['name']}/Index/workbench";
        }
        return $result;
    }

    /**
     * 获取插件工具栏路由
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getToolbarRoute()
    {
        $data   = self::getWebAuthPlugin();
        $result = [];
        foreach ($data as $item) {
            $class = "\\plugin\\{$item['name']}\\app\\controller\\IndexController";
            if (!class_exists($class)) {
                continue;
            }
            $controller = new $class();
            if (!method_exists($controller, 'toolbar')) {
                continue;
            }
            $result[$item['name']] = "app/{$item['name']}/Index/toolbar";
        }
        return $result;
    }
}
