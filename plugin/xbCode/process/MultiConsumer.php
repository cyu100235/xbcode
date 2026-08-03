<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\process;

use support\Log;
use support\Redis;
use support\Context;
use support\Container;
use Webman\RedisQueue\Client;

/**
 * 多目录队列消费者进程
 * 自动扫描 plugin 目录下所有插件，存在 app/queue 目录则加入消费列表
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class MultiConsumer
{
    /**
     * 已注册的消费者实例
     * @var array
     */
    protected $_consumers = [];

    /**
     * 启动进程
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onWorkerStart()
    {
        // 检测 Redis 连接是否成功（通过 ping 发起真实网络连接）
        try {
            if (!Redis::ping()) {
                echo "Queue Redis connection failed: ping returned false\r\n";
                return;
            }
        } catch (\Throwable $e) {
            echo "Queue Redis connection failed: {$e->getMessage()}\r\n";
            return;
        }
        foreach ($this->resolveConsumerDirs() as $dir) {
            $this->loadConsumersFromDir($dir);
        }
    }

    /**
     * 扫描插件目录，收集存在 app/queue 目录的插件
     * @return string[]
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function resolveConsumerDirs(): array
    {
        $dirs = [];
        $pluginDir = base_path('plugin');
        if (!is_dir($pluginDir)) {
            return $dirs;
        }
        foreach (new \DirectoryIterator($pluginDir) as $item) {
            if ($item->isDot() || !$item->isDir()) {
                continue;
            }
            $queueDir = $item->getPathname() . '/app/queue';
            if (is_dir($queueDir)) {
                $dirs[] = $queueDir;
            }
        }
        return $dirs;
    }

    /**
     * 扫描单个目录并注册消费者
     * @param string $dir
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function loadConsumersFromDir(string $dir): void
    {
        if (!is_dir($dir)) {
            echo "Consumer directory {$dir} not exists\r\n";
            return;
        }
        // 递归扫描目录
        $dir_iterator = new \RecursiveDirectoryIterator($dir);
        // 递归迭代器
        $iterator = new \RecursiveIteratorIterator($dir_iterator);

        foreach ($iterator as $file) {
            if (is_dir($file)) {
                continue;
            }
            $fileinfo = new \SplFileInfo($file);
            if ($fileinfo->getExtension() !== 'php') {
                continue;
            }

            $class = str_replace('/', "\\", substr(substr($file, strlen(base_path())), 0, -4));
            if (!is_a($class, 'Webman\RedisQueue\Consumer', true)) {
                continue;
            }

            $consumer = Container::get($class);
            $connection_name = $consumer->connection ?? 'default';
            $queue = $consumer->queue ?? (new \ReflectionClass($consumer))->getShortName();

            if (!$queue) {
                echo "Consumer {$class} queue not exists\r\n";
                continue;
            }

            $this->_consumers[$queue] = $consumer;
            $connection = Client::connection($connection_name);

            $consumer_func = function ($message) use ($consumer) {
                try {
                    $consumer->consume($message);
                } catch (\Throwable $e) {
                    throw $e;
                } finally {
                    Context::destroy();
                }
            };

            $connection->subscribe($queue, $consumer_func);

            if (method_exists($connection, 'onConsumeFailure')) {
                $connection->onConsumeFailure(function ($exception, $package) {
                    $consumer = $this->_consumers[$package['queue']] ?? null;
                    if ($consumer && method_exists($consumer, 'onConsumeFailure')) {
                        call_user_func([$consumer, 'onConsumeFailure'], $exception, $package);
                    }
                });
            }
        }
    }
}
