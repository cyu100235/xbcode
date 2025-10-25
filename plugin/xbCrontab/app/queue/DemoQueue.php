<?php
namespace plugin\xbCrontab\app\queue;

use Exception;
use Webman\RedisQueue\Consumer;

/**
 * 队列任务示例
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class DemoQueue implements Consumer
{
    /**
    * 投递方式如下：
    * 同步队列任务投递
    * \xbcode\providers\QueueProvider::add('DemoQueue', [], 'xbCrontab');
    * 异步队列任务投递
    * \xbcode\providers\QueueProvider::addAsync('DemoQueue', [], 'xbCrontab', 10);
    */

    /**
     * 队列消费
     * @param mixed $data 队列任务参数
     * @return void
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function consume($data)
    {
        print_r($data);
    }
}