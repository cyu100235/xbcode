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
namespace plugin\xbCode\api;

use Webman\RedisQueue\Redis;
use Webman\RedisQueue\Client;

/**
 * 队列相关接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class QueueApi
{
    /**
     * 创建实例
     * @return QueueApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 投递同步队列
     * @param string $name 队列名称（示例：xb_demo_queue）
     * @param array $data 队列数据
     * @param int $delay 延迟时间（可选，单位：秒）
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function send(string $name, array $data = [], int $delay = 0)
    {
        Redis::send($name, $data, $delay);
    }

    /**
     * 投递异步队列
     * @param string $name 队列名称（示例：xb_demo_queue）
     * @param array $data 队列数据
     * @param int $delay 延迟时间（可选，单位：秒）
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function sendAsync(string $name, array $data = [], int $delay = 0)
    {
        Client::send($name, $data, $delay);
    }
}