<?php
namespace plugin\xbCode\utils;

use app\process\Monitor;
use Workerman\Timer;
use Workerman\Worker;
use support\Log;

/**
 * 框架工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class FrameUtil
{
    /**
     * 延迟重启
     * @param int $second
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function delayReload(int $second)
    {
        self::pcntlAlarm($second, function () {
            self::reload();
        });
    }
    
    /**
     * 异步延迟执行代码
     * @param int $second 延迟秒数
     * @param callable $callback 回调函数
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pcntlAlarm(int $second, callable $callback)
    {
        // 设置延迟执行回调
        pcntl_signal(SIGALRM, $callback);
        // 设置延迟执行时间
        pcntl_alarm($second);
    }

    /**
     * 平滑启动
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function reload()
    {
        if (function_exists('posix_kill') && function_exists('posix_getppid')) {
            // 所有子进程重启
            try {
                posix_kill(posix_getppid(), SIGUSR1);
                return true;
            } catch (\Throwable $e) {
                Log::error("平滑启动失败：" . $e->getMessage());
                return false;
            }
        } else {
            // 重启当前子进程
            Timer::add(1, function () {
                Worker::stopAll();
            });
        }
        return false;
    }

    /**
     * 停止服务
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function stop()
    {
        Worker::stopAll();
    }

    /**
     * 暂停文件监控
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pauseFileMonitor()
    {
        if (method_exists(Monitor::class, 'pause')) {
            Monitor::pause();
        }
    }

    /**
     * 恢复文件监控
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function resumeFileMonitor()
    {
        if (method_exists(Monitor::class, 'resume')) {
            Monitor::resume();
        }
    }
}
