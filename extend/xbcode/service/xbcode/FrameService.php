<?php
namespace xbcode\service\xbcode;

use Exception;
use support\Cache;

/**
 * 框架服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class FrameService extends XbBaseService
{
    /**
     * 获取框架版本
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function version(string $localVersion)
    {
        $data = Cache::get('xb_frame_version');
        if ($data) {
            return $data;
        }
        $result = static::request()->get('Frame/version',[
            'local_version' => $localVersion
        ])->array();
        if (empty($result)) {
            throw new Exception('网络请求错误');
        }
        Cache::set('xb_frame_version', $result, self::$cacheTime);
        return $result;
    }
    
    /**
     * 检测是否有新版本
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checked()
    {
        // 本地版本
        $localVersion = config('projects.version');
        if (empty($localVersion)) {
            throw new Exception('本地版本号错误');
        }
        // 删除更新缓存
        Cache::delete('xb_frame_version');
        // 获取版本信息
        $result        = self::version($localVersion);
        // 服务器版本
        $serverVersion = $result['data']['version'] ?? '';
        // 检测是否有更新
        $status = version_compare($serverVersion, $localVersion, '>');
        // 返回数据
        return (bool)$status;
    }
}