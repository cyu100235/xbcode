<?php
namespace app\model;

use xbcode\Model;
use support\Cache;

/**
 * 站点模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebSite extends Model
{
    /**
     * 获取站点字典
     * @param bool $force 是否强制刷新
     * @param mixed $fields 查询字段
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getWebSiteDict(bool $force = false, mixed $fields = '*')
    {
        $key = 'xb_web_site_dict';
        $result = Cache::get($key);
        if ($result && !$force) {
            return $result;
        }
        $data = self::column($fields, 'domain');
        // 缓存站点字典
        Cache::set($key, $data, 600);
        // 返回站点字典
        return $data;
    }
}
