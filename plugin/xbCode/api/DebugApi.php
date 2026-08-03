<?php
namespace plugin\xbCode\api;

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
        $debug = env('APP_DEBUG');
        if (is_string($debug)) {
            $debug = filter_var($debug, FILTER_VALIDATE_BOOLEAN);
        }
        if (is_string($debug)) {
            $debug = $debug === 'true';
        }
        return $debug;
    }
}