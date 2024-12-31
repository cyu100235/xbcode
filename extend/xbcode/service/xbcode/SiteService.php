<?php
namespace xbcode\service\xbcode;

use Exception;
use support\Cache;

/**
 * 站点服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SiteService extends XbBaseService
{
    /**
     * 获取站点详情
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function detail()
    {
        $data = Cache::get('xb_site_detail');
        if ($data) {
            return $data;
        }
        $result = self::request()->get('Site/detail')->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        if (!isset($result['code']) && $result['code'] != 200) {
            throw new Exception('获取站点详情错误');
        }
        if (empty($result['data'])) {
            throw new Exception('获取站点详情失败');
        }
        $data = $result['data'];
        $data['expire_state'] = false;
        if ($data['expire_time']) {
            // 判断是否过期
            $expireTime = strtotime($data['expire_time']);
            if (time() < $expireTime) {
                $data['expire_state'] = true;                
            }
        }
        Cache::set('xb_site_detail', $data, self::$cacheTime);
        return $data;
    }
}