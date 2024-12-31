<?php

namespace app\model;

use support\Cache;
use xbcode\Model;

/**
 * 站点插件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebPlugin extends Model
{
    /**
     * 获取站点授权插件
     * @param bool $force 是否强制刷新
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getWebAuthPlugin(bool $force = false)
    {
        $key = "xb_web_auth_plugin";
        $data    = Cache::get($key);
        if ($data && !$force) {
            return $data;
        }
        // 获取授权插件
        $data = $this->select()->toArray();
        // 设置授权插件缓存
        Cache::set($key, $data, 600);
        // 返回数据
        return $data;
    }
}
