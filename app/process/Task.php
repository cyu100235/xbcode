<?php
namespace app\process;

use app\model\Crontab;

/**
 * 定时任务处理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Task
{
    /**
     * 定时任务处理
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function onWorkerStart()
    {
        // 检测数据库非正常连接
        $database = config('thinkorm.connections.mysql');
        if (empty($database['database']) || empty($database['username'])) {
            return;
        }
        // 获取定时任务
        $data = Crontab::getCrontabCache();
        if (empty($data)) {
            return;
        }
        foreach ($data as $item) {
            // 投递定时任务
            new \Workerman\Crontab\Crontab(
                $item['expression'],
                function ()use($item) {
                    self::execTask($item);
                }
            );
        }
    }

    /**
     * 执行任务
     * @param array $task
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function execTask(array $task)
    {
        // 开始执行
        $startTime = microtime(true);
        try {
            if (!empty($item['params'])) {
                exec("{$task['command']} {$task['params']}");
            } else {
                exec($task['command']);
            }
            // 清除错误信息
            Crontab::where('id', $task['id'])->update(['error' => '']);
        } catch (\Throwable $e) {
            // 记录错误信息
            Crontab::where('id', $task['id'])->update([
                'error' => $e->getMessage(),
                'state' => '30'
            ]);
        } finally {
            $endTime = microtime(true);
            // 本次执行时间
            $useTime = round(($endTime - $startTime), 2);
            // 最大执行时间
            $maxTime = max($useTime, $task['max_time']);
            // 更新最后执行时间
            Crontab::where('id', $task['id'])->update([
                'last_time' => date('Y-m-d H:i:s'),
                'run_time' => $useTime,
                'max_time' => $maxTime
            ]);
        }
    }
}