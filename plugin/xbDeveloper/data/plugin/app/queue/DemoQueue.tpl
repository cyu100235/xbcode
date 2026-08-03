<?php
namespace plugin\{PLUGIN_NAME}\app\queue;

use Exception;
use Webman\RedisQueue\Consumer;

/**
 * 队列任务示例
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class DemoQueue implements Consumer
{
    /**
     * 队列名称（不填写则自动以当前类名作为队列名）
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    // public $queue = 'xbcode_demo';

    /**
     * 队列消费
     * @param mixed $data 队列任务参数
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function consume($data)
    {
        print_r($data);
    }
}