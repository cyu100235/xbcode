<?php

namespace app\common\service\cloud;
use think\facade\Cache;

/**
 * 开发者应用云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait DeveloperCloud
{
    /**
     * 获取已安装的开发者应用列表
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAuthorAppList()
    {
        if (Cache::get('developer_mode','10') == '10') {
            return [];
        }
        $user = self::user();
        if (!isset($user['data']['is_dev'])) {
            return [];
        }
        if ($user['data']['is_dev'] != '30') {
            return [];
        }
        $data = self::send('Developer/getAppList')->array();
        if (!isset($data['code'])) {
            return [];
        }
        if ($data['code'] != 200) {
            return [];
        }
        return $data['data'];
    }
    
    /**
     * 设置/获取开发者模式
     * @param string $mode
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function developerMode(string $mode = '')
    {
        if ($mode) {
            Cache::set('developer_mode', $mode);
        }
        return Cache::get('developer_mode','10');
    }

    /**
     * 获取框架版本列表
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getFrameVersion()
    {
        return self::send('Frame/versionList')->array();
    }
    
    /**
     * 发布开发者应用
     * @param array $params
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function publishAuthorApp(array $params)
    {
        return self::send('Developer/publish',$params)->array();
    }
}
