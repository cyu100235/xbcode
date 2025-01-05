<?php
namespace xbcode\providers;

use Webman\RedisQueue\Redis;

/**
 * 队列服务提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class QueueProvider
{
    /**
     * 主任务名称
     * @var string
     */
    private static $mainTaskName = 'xbcode_queue_task';

    /**
     * 投递同步队列任务
     * @param string $name 队列名称
     * @param array $data 投递数据
     * @param string $plugin 插件名称
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function add(string $name, array $data, string $plugin = '')
    {
        $result = self::getResult($name, $data, $plugin);
        return Redis::send(self::$mainTaskName, $result);
    }

    /**
     * 投递异步队列任务
     * @param string $name 队列名称
     * @param array $data 投递数据
     * @param string $plugin 插件名称
     * @param int $delay 延迟时间
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function addAsync(string $name, array $data, string $plugin = '', int $delay = 60)
    {
        $result = self::getResult($name, $data, $plugin);
        return Redis::send(self::$mainTaskName, $result, $delay);
    }

    /**
     * 获取队列任务数据
     * @param string $name 队列名称
     * @param array $data 投递数据
     * @param string $plugin 插件名称
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getResult(string $name, array $data, string $plugin = '')
    {
        return [
            // 任务消费类名称
            'name' => $name,
            // 插件名称
            'plugin' => $plugin,
            // 任务数据
            'extends' => $data
        ];
    }
}