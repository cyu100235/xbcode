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
use plugin\xbCode\api\Mysql;
use Workerman\Crontab\Crontab;
use plugin\xbCrontab\api\TaskApi;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\utils\FrameUtil;
use plugin\xbCrontab\api\ChannelClient;
use plugin\xbCrontab\app\model\CrontabLog;
use plugin\xbCrontab\app\model\Crontab as CrontabModel;

/**
 * 定时任务进程
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
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $crontabs = [];

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
        // 直接使用 cron_expression
        $expression = $crontab['cron_expression'] ?? '';
        if (empty($expression)) {
            return;
        }
        $crontabObj = new Crontab($expression, function () use ($crontab) {
            // 执行任务
            $this->runCrontab($crontab);
        });
        // 任务标识 => 定时任务对象id
        $this->crontabs[$name] = $crontabObj->getId();
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
        // 如果任务已存在，则删除
        if (isset($this->crontabs[$name])) {
            Crontab::remove($this->crontabs[$name]);
            unset($this->crontabs[$name]);
        }
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
        // 开始时间
        $startTime = microtime(true);
        try {
            // 获取任务执行类实例
            $taskApi = TaskApi::make();
            // 获取可执行方法列表
            $methods = $taskApi->getMethods();
            // 获取任务类型
            $type = $crontab['type'];
            if (!isset($methods[$type])) {
                throw new Exception("任务类型:{$type}，不存在");
            }
            // 调用任务执行方法
            $method = $methods[$type];
            $taskApi->$method($crontab['command']);
            // 获取结束时间
            $endTime = microtime(true);
            // 记录任务耗时(秒)
            $runSecondTime = $endTime - $startTime;
            // 更新数据
            $model->save([
                'last_time' => date('Y-m-d H:i:s'),
                'error' => '',
            ]);
            // 记录执行日志
            $logModel = new CrontabLog;
            $logModel->save([
                'crontab_id' => $model['id'],
                'run_second_time' => $runSecondTime,
                'remarks' => '任务执行完成',
            ]);
        } catch (\Throwable $th) {
            // 获取结束时间
            $endTime = microtime(true);
            // 记录任务耗时(秒)
            $runSecondTime = $endTime - $startTime;
            // 更新数据
            $model->save([
                'last_time' => date('Y-m-d H:i:s'),
                'state' => '30',
                'error' => $th->getMessage(),
            ]);
            // 记录执行日志
            $logModel = new CrontabLog;
            $logModel->save([
                'crontab_id' => $model['id'],
                'run_second_time' => $runSecondTime,
                'remarks' => $th->getMessage(),
            ]);
            // 更新任务
            $name = $this->getCrontabName($crontab);
            $this->updateCrontab($name, $crontab);
        }
    }

    /**
     * 获取定时任务标识
     * @param array $crontab
     * @throws \Exception
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