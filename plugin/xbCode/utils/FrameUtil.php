<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\utils;

use Exception;
use support\Log;
use Workerman\Timer;
use Workerman\Worker;
use app\process\Monitor;
use plugin\xbCode\api\PluginsApi;

/**
 * 框架工具类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class FrameUtil
{
    /**
     * 检测框架是否已安装
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function checked()
    {
        $path = base_path('.env');
        if (!is_file($path)) {
            return false;
        }
        return filesize($path) > 0;
    }

    /**
     * 检查端口是否被占用
     * @param int $port 端口号
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function checkedPort(int $port): bool
    {
        try {
            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($socket === false) {
                return false;
            }
            $result = socket_bind($socket, '0.0.0.0', $port);
            socket_close($socket);
        } catch (\Throwable $th) {
            if (str_contains($th->getMessage(), 'Address already in use')) {
                return false;
            }
            throw $th;
        }
        // 返回结果
        return $result !== false;
    }

    /**
     * 延迟重启
     * @param int $second 延迟秒数
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function pcntlAlarm(int $second, callable $callback)
    {
        if (!function_exists('pcntl_signal')) {
            return;
        }
        if (!function_exists('pcntl_alarm')) {
            return;
        }
        if (!defined('SIGALRM')) {
            return;
        }
        // 设置延迟执行回调
        pcntl_signal(SIGALRM, $callback);
        // 设置延迟执行时间
        pcntl_alarm($second);
    }

    /**
     * 平滑启动
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function reload()
    {
        if (function_exists('posix_kill') && function_exists('posix_getppid') && defined('SIGUSR1')) {
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function stop()
    {
        Worker::stopAll();
    }

    /**
     * 暂停文件监控
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function xbServerPort(int $default = 39000)
    {
        // 检测本地伪静态文件
        $file = base_path() . '/nginx.conf';
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

    /**
     * 更新服务端口号
     * @param int $port
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function updateServerPort(int $port)
    {
        $file = base_path() . '/nginx.conf';
        if (!file_exists($file)) {
            throw new Exception('nginx配置文件不存在');
        }
        $content = file_get_contents($file);
        if (empty($content)) {
            throw new Exception('nginx配置文件内容为空');
        }
        // 替换端口号
        $content = preg_replace('/proxy_pass\s+http:\/\/\d+\.\d+\.\d+\.\d+:\d+/', "proxy_pass http://127.0.0.1:{$port}", $content);
        // 写入文件
        file_put_contents($file, $content);
    }

    /**
     * 获取默认首页路由
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getHomeRoute()
    {
        // 默认首页路由
        $homeRoute = [
            \plugin\xbCode\app\index\controller\IndexController::class,
            'index',
        ];
        // 检测框架是否已安装
        if (!FrameUtil::checked()) {
            return [
                'home' => $homeRoute,
                'middlewares' => [],
            ];
        }
        // 获取核心插件
        $pluginNames = PluginsApi::make()->getSystemPluginNames();
        // 获取所有插件的xbcode.php配置文件
        $xbCodeConfig = glob(base_path() . '/plugin/*/config/xbcode.php');
        // 占位符
        $temp = [];
        // 中间件
        $homeMiddleware = [];
        foreach ($xbCodeConfig as $configPath) {
            // 获取插件标识
            $pluginName = basename(dirname($configPath, 2));
            // 检测是否核心插件
            if (in_array($pluginName, $pluginNames)) {
                continue;
            }
            $route = include $configPath;
            if (empty($route)) {
                continue;
            }
            // 验证插件是否安装
            if (!PluginsApi::make()->hasEnabled($pluginName)) {
                continue;
            }
            if (empty($route['home'])) {
                continue;
            }
            $temp = $route['home'];
            $homeRoute = $route['home'];
            $homeMiddleware = $route['middlewares'] ?? [];
        }
        return [
            'home' => $homeRoute,
            'middlewares' => $homeMiddleware,
        ];
    }

    /**
     * 获取后台地址
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getBackendUrl()
    {
        $adminModule = self::getBackend();
        $url = "/{$adminModule}";
        return $url;
    }

    /**
     * 获取后台地址模块
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getBackend()
    {
        return env('ADMIN_URL', 'backend');
    }

    /**
     * 获取重定向地址
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getRedirectUrl()
    {
        $files = glob(base_path() . '/plugin/*/config/xbcode.php');
        $redirect = '';
        foreach ($files as $path) {
            $pluginName = basename(dirname($path, 2));
            $config = include $path;
            if (empty($config['redirect'])) {
                continue;
            }
            if (!empty($redirect)) {
                continue;
            }
            // 检测插件是否安装
            if (!PluginsApi::make()->hasEnabled($pluginName)) {
                continue;
            }
            $redirect = $config['redirect'];
            return $redirect;
        }
        return self::getBackendUrl();
    }
}
