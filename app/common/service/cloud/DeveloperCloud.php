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
     * 发布开发者应用
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function publishAuthorApp()
    {
        return self::send('Developer/publish')->array();
    }
}
