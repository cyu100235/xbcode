<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://www.xbcode.net/documents
 */
namespace plugin\xbCrontab\app\process;

use Exception;
use Workerman\Timer;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\utils\FrameUtil;
use plugin\xbCrontab\api\CrontabApi;
use plugin\xbCrontab\api\ChannelClient;
use plugin\xbCrontab\api\CronExpressionApi;
use plugin\xbCrontab\app\model\Crontab as CrontabModel;

/**
 * 定时任务进程
 *
 * 采用“每秒比对”的调度方式：进程内维持一个 1 秒的定时器，
 * 每秒检查一次每个任务的 Cron 表达式是否命中当前时间并触发执行。
 * 5 位表达式（分 时 日 月 周）为分钟级，6 位表达式（秒 分 时 日 月 周）为秒级。
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class XbCrontab
{
    /**
     * 定时任务更新事件名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static $eventName = 'task_update';

    /**
     * 任务列表
     * @var array<string, array{expression:string, data:array, last_second:int}>
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $crontabs = [];

    /**
     * 调度定时器ID
     * @var int|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $tickTimerId = null;

    /**
     * 进程启动
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onWorkerStart()
    {
        // 检测是否已安装
        if (!FrameUtil::checked()) {
            return;
        }
        // 检测是否已安装该插件
        if (!PluginsApi::make()->installed('xbCrontab')) {
            return;
        }
        // 检测数据表是否存在
        if (!Mysql::hasTable('crontab')) {
            return;
        }
        // 初始化任务
        $this->initCrontab();
        // 监听任务变动
        $this->listenCrontab();
        // 启动调度器
        $this->startScheduler();
    }

    /**
     * 初始化任务
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function initCrontab()
    {
        // 获取全部定时任务
        $crontab = CrontabModel::where('state', '20')->select()->toArray();
        // 遍历任务
        foreach ($crontab as $item) {
            // 任务标识
            $name = $this->getCrontabName($item);
            // 添加任务
            $this->addCrontab($name, $item);
        }
    }

    /**
     * 监听任务变动
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function listenCrontab()
    {
        // 订阅Channel事件(定时任务更新、添加、删除)触发
        ChannelClient::subscribe(static::$eventName, function ($data) {
            $name = $this->getCrontabName($data['data']);
            $crontab = $data['data'];
            $this->updateCrontab($name, $crontab);
        });
    }

    /**
     * 启动调度器（每秒比对一次，支持秒级与分钟级）
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function startScheduler()
    {
        // 避免重复启动
        if ($this->tickTimerId !== null) {
            return;
        }
        // 每秒执行一次比对
        $this->tickTimerId = Timer::add(1, function () {
            $this->tick();
        });
    }

    /**
     * 时钟比对：命中表达式的任务触发执行
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function tick()
    {
        $now = time();
        $cron = CronExpressionApi::make();
        foreach ($this->crontabs as $name => $task) {
            // 同一秒内避免重复触发
            if (($task['last_second'] ?? 0) === $now) {
                continue;
            }
            // 未命中表达式则跳过
            if (!$cron->matches($task['expression'], $now)) {
                continue;
            }
            // 记录触发秒，保持任务数据同步
            $this->crontabs[$name]['last_second'] = $now;
            // 执行任务
            $this->runCrontab($task['data']);
        }
    }

    /**
     * 添加任务
     * @param string $name 任务标识
     * @param array $crontab 任务数据
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function addCrontab(string $name, array $crontab)
    {
        // 任务状态
        $state = $crontab['state'] ?? '10';
        if ($state !== '20') {
            return;
        }
        // 表达式
        $expression = trim((string) ($crontab['cron_expression'] ?? ''));
        // 表达式为空或非法则不注册
        if ($expression === '' || !CronExpressionApi::make()->supports($expression)) {
            return;
        }
        // 注册任务（记录表达式、任务数据、上次触发的秒）
        $this->crontabs[$name] = [
            'expression' => $expression,
            'data' => $crontab,
            'last_second' => 0,
        ];
    }

    /**
     * 更新任务
     * @param string $name 任务标识
     * @param array $crontab 任务数据
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function updateCrontab(string $name, array $crontab)
    {
        // 先移除旧任务
        unset($this->crontabs[$name]);
        $where = [
            'plugin' => $crontab['plugin'],
            'name' => $crontab['name'],
            'state' => '20',
        ];
        $model = CrontabModel::where($where)->find();
        if ($model) {
            // 继续添加任务
            $this->addCrontab($name, $crontab);
        }
    }

    /**
     * 运行任务
     * @param array $crontab 任务数据
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function runCrontab(array $crontab)
    {
        // 获取任务数据
        $where = [
            'id' => $crontab['id'],
            'state' => '20',
        ];
        $model = CrontabModel::where($where)->find();
        if (!$model) {
            return;
        }
        try {
            // 执行任务
            CrontabApi::make()->run($model);
        } catch (\Throwable $th) {
            // 更新任务
            $name = $this->getCrontabName($crontab);
            $this->updateCrontab($name, $crontab);
        }
    }

    /**
     * 获取定时任务标识
     * @param array $crontab
     * @throws Exception
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getCrontabName(array $crontab)
    {
        if (empty($crontab['plugin']) || empty($crontab['name'])) {
            throw new Exception('获取定时任务标识失败');
        }
        return "{$crontab['plugin']}_{$crontab['name']}";
    }
}