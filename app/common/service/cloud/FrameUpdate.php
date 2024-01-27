<?php

namespace app\common\service\cloud;

/**
 * 框架升级服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait FrameUpdate
{
    // 获取框架升级信息
    public static function getFrameUpdate()
    {
        return self::send('FrameUpdate/getFrameUpdate');
    }
    // 下载框架升级包
    public static function downloadFrame()
    {
    }

    // 获取框架升级日志
    public static function getFrameLogList()
    {
    }
    
    // 获取框架授权信息
    public static function getFrameAuth()
    {
    }
}
