<?php

namespace app\queue\redis;

use support\Log;
use think\helper\Str;
use Webman\RedisQueue\Consumer;

/**
 * 队列任务转发
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbQueueTask implements Consumer
{
    // 消费的队列名
    public $queue = 'xbcode_queue_task';

    /**
     * 队列消费
     * @param mixed $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function consume($data)
    {
        try {
            if (empty($data['name'])) {
                return;
            }
            // 类名
            $name = Str::studly($data['name']);
            if (empty($data['plugin'])) {
                $class = "\\app\\queue\\{$name}";
            } else {
                $class = "\\plugin\\{$data['plugin']}\\queue\\{$name}";
            }
            if (!class_exists($class)) {
                return;
            }
            $class = new $class;
            if (method_exists($class, 'consume')) {
                $result = empty($data['extends']) ? [] : $data['extends'];
                $class->consume($result);
            }
        } catch (\Throwable $th) {
            Log::error("队列任务转发失败：{$th->getMessage()}");
            throw $th;
        }
    }
}