<?php
namespace app\common\utils;

use Exception;
use process\Monitor;
use support\Log;
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
        if (function_exists('posix_kill')) {
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

    /**
     * 获取服务端口
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getServerPort()
    {
        $path = base_path('nginx.rewrite');
        if (!file_exists($path)) {
            throw new Exception('nginx.rewrite文件不存在');
        }
        $content = file_get_contents($path);
        // 获取端口号
        preg_match('/127.0.0.1:(.*);/', $content, $matches);
        $port = $matches[1] ?? '';
        return $port;
    }
}
