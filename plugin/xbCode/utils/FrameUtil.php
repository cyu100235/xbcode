<?php
namespace plugin\xbCode\utils;

use support\Log;
use Workerman\Timer;
use Workerman\Worker;
use app\process\Monitor;

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

    /**
     * 解析nginx配置文件
     * @param string $file
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function xbGetNginxConf(string $file)
    {
        // 读取文件内容
        $content = file_get_contents($file);
        if (empty($content)) {
            return null;
        }
        // 解析nginx配置文件
        preg_match('/proxy_pass\s+http:\/\/\d+\.\d+\.\d+\.\d+:(\d+)/', $content, $matches);
        // 获取端口号
        $port = $matches[1] ?? null;
        return $port;
    }
    
    /**
     * 获取服务端口号
     * @param int $default
     * @return int
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function xbServerPort(int $default = 39000)
    {
        // 获取宝塔环境配置
        $btEnv = getenv('BT_ENV_STATE') === 'true';
        $name = getenv('BT_ENV_NAME');
        $file = "/www/server/panel/vhost/rewrite/{$name}.conf";
        // 检测是否宝塔环境
        if ($btEnv && $name && file_exists($file)) {
            // 获取nginx配置文件端口号
            $port = static::xbGetNginxConf($file);
            if ($port) {
                return (int) $port;
            }
        }
        // 检测本地伪静态文件
        $file = base_path() . '/nginx.conf';
        if (file_exists($file)) {
            // 获取nginx配置文件端口号
            $port = static::xbGetNginxConf($file);
            if ($port) {
                return (int) $port;
            }
        }
        // 检测插件伪静态文件
        $file = base_path() . '/plugin/xbCode/nginx.conf';
        if (file_exists($file)) {
            // 获取nginx配置文件端口号
            $port = static::xbGetNginxConf($file);
            if ($port) {
                return (int) $port;
            }
        }
        // 返回默认端口号
        return $default;
    }
}
