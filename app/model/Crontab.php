<?php

namespace app\model;

use support\Cache;
use xbcode\Model;

/**
 * 定时任务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Crontab extends Model
{
    /**
     * 获取缓存
     * @param bool $force 是否强制刷新缓存
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getCrontabCache(bool $force = false)
    {
        $data = Cache::get('crontab', []);
        if ($data && !$force) {
            return $data;
        }
        // 获取数据
        $data = self::setCrontabCache();
        return $data;
    }

    /**
     * 设置缓存
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function setCrontabCache()
    {
        $where = [
            'state' => '20'
        ];
        $data  = self::where($where)->select()->toArray();
        Cache::set('crontab', $data, 600);
        return $data;
    }
}
