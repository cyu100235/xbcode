<?php
namespace plugin\xbCode\api;

use Exception;

/**
 * 调试状态
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class DebugApi
{
    /**
     * 获取调试状态
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function status()
    {
        $debug = getenv('APP_DEBUG') === 'true';
        return $debug;
    }
}