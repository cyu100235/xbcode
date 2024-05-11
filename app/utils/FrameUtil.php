<?php
namespace app\utils;
use process\Monitor;
use Workerman\Timer;
use Workerman\Worker;

/**
 * 框架工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class FrameUtil
{
     /**
     * 获取某个composer包的版本
     * @param string $package
     * @return mixed|string
     */
    public static function getPackageVersion(string $package)
    {
        $installed_php = base_path('vendor/composer/installed.php');
        if (is_file($installed_php)) {
            $packages = include $installed_php;
        }
        return substr($packages['versions'][$package]['version'] ?? 'unknown  ', 0, -2);
    }

    /**
     * 异步延迟执行代码
     * @param int $second
     * @param callable $callback
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function pcntlAlarm(int $second,callable $callback)
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
        if (function_exists('posix_kill')) {
            try {
                posix_kill(posix_getppid(), SIGUSR1);
                return true;
            } catch (\Throwable $e) {}
        } else {
            Timer::add(1, function () {
                Worker::stopAll();
            });
        }
        return false;
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
