<?php
namespace plugin\{PLUGIN_NAME}\app\queue;

use Exception;
use Webman\RedisQueue\Consumer;

/**
 * 队列任务示例
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DemoQueue implements Consumer
{
    /**
    * 投递方式如下：
    * 同步队列任务投递
    * \xbcode\providers\QueueProvider::add('DemoQueue', [], '{PLUGIN_NAME}');
    * 异步队列任务投递
    * \xbcode\providers\QueueProvider::addAsync('DemoQueue', [], '{PLUGIN_NAME}', 10);
    */

    /**
     * 队列消费
     * @param mixed $data 队列任务参数
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function consume($data)
    {
        print_r($data);
    }
}