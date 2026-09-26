<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\app\process;

use Throwable;
use RuntimeException;
use Workerman\Timer;
use Workerman\Worker;
use support\Log;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\enum\TaskTypeEnum;
use plugin\xbAdmin\enum\ExecuteModeEnum;
use plugin\xbAdmin\app\model\Crontab as CrontabModel;

/**
 * 定时任务调度进程
 *
 * 任务周期是「固定间隔」，用 Workerman 原生定时器实现：进程启动时把数据库中启用的任务
 * 按 rule 换算成间隔秒数注册为持久定时器，之后每隔 SYNC_INTERVAL 秒扫描一次，
 * 按 rule 的哈希比对增删改，实现后台改表即生效。
 * 执行时按 mode 分流：php 走「执行目标 + 执行参数」，command 再按 type 走 Shell 命令、URL 或 PHP 代码。
 * 该进程不监听端口，仅做调度。
 * @copyright 贵州云铺网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Crontab
{
    /**
     * 数据库同步间隔（秒）
     */
    protected const SYNC_INTERVAL = 30;

    /**
     * 已注册的任务
     * @var array<int, array{hash: string, timer: int}>
     */
    protected array $tasks = [];

    /**
     * 进程启动
     *
     * 必须在 onWorkerStart 中注册定时器：Timer::add 依赖已就绪的事件循环。
     * @param Worker $worker
     * @return void
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function onWorkerStart(Worker $worker): void
    {
        $this->sync();
        Timer::add(static::SYNC_INTERVAL, function () {
            $this->sync();
        });
    }

    /**
     * 同步数据库中的任务
     * @return void
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function sync(): void
    {
        try {
            $rows = CrontabModel::where('state', StateEnum::ENABLED['value'])->select()->toArray();
        } catch (Throwable $e) {
            Log::warning('定时任务同步失败：' . $e->getMessage());
            return;
        }
        $exists = [];
        foreach ($rows as $row) {
            $id = (int) ($row['id'] ?? 0);
            $rule = trim((string) ($row['rule'] ?? ''));
            if ($id <= 0 || $rule === '') {
                continue;
            }
            $exists[$id] = true;
            // 周期配置未变化，沿用已注册的定时器
            $hash = md5($rule);
            $current = $this->tasks[$id] ?? null;
            if ($current && $current['hash'] === $hash) {
                continue;
            }
            if ($current) {
                Timer::del($current['timer']);
                unset($this->tasks[$id]);
            }
            $seconds = CrontabModel::toSeconds(CrontabModel::parseRule($rule));
            if ($seconds < 1) {
                Log::warning("定时任务执行周期非法，已跳过：crontab-{$id} {$rule}");
                continue;
            }
            $this->tasks[$id] = [
                'hash' => $hash,
                'timer' => Timer::add($seconds, function () use ($id) {
                    $this->run($id);
                }),
            ];
        }
        // 已删除或已停用的任务，销毁定时器
        foreach (array_keys($this->tasks) as $id) {
            if (!isset($exists[$id])) {
                Timer::del($this->tasks[$id]['timer']);
                unset($this->tasks[$id]);
            }
        }
    }

    /**
     * 执行任务
     * @param int $id 任务ID
     * @return void
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function run(int $id): void
    {
        $model = CrontabModel::where('id', $id)->find();
        if (!$model || (string) $model['state'] !== StateEnum::ENABLED['value']) {
            return;
        }
        try {
            $this->execute($model);
        } catch (Throwable $e) {
            Log::warning("定时任务执行失败：crontab-{$id} " . $e->getMessage());
            return;
        }
        try {
            $model->save([
                'last_time' => date('Y-m-d H:i:s'),
                'run_count' => (int) $model['run_count'] + 1,
            ]);
        } catch (Throwable $e) {
            Log::warning("定时任务执行结果回写失败：crontab-{$id} " . $e->getMessage());
        }
    }

    /**
     * 按执行方式执行任务
     *
     * 配置非法或执行出错时抛异常，由 run() 统一记录日志并跳过结果回写。
     * @param CrontabModel $model
     * @throws RuntimeException
     * @return void
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(CrontabModel $model): void
    {
        if ((string) $model['mode'] === ExecuteModeEnum::COMMAND['value']) {
            $this->executeCommand($model);
            return;
        }
        $target = trim((string) $model['target']);
        if (!preg_match('/^[\\\\\w]+::[\\\\\w]+$/', $target)) {
            throw new RuntimeException("执行目标非法：{$target}");
        }
        [$class, $method] = explode('::', $target, 2);
        if (!class_exists($class) || !method_exists($class, $method)) {
            throw new RuntimeException("执行目标不存在：{$target}");
        }
        // 参数数组按位置顺序传入，避免 PHP8 把字符串键当作命名参数
        call_user_func_array([$class, $method], CrontabModel::parseParams((string) $model['params']));
    }

    /**
     * 按任务类型执行命令
     * @param CrontabModel $model
     * @throws RuntimeException
     * @return void
     * @copyright 贵州云铺网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function executeCommand(CrontabModel $model): void
    {
        $command = trim((string) $model['command']);
        if ($command === '') {
            throw new RuntimeException('任务命令为空');
        }
        switch ((string) $model['type']) {
            case TaskTypeEnum::SHELL['value']:
                $cwd = getcwd();
                chdir(base_path());
                exec($command, $output, $code);
                if ($cwd !== false) {
                    chdir($cwd);
                }
                if ($code !== 0) {
                    throw new RuntimeException("执行Shell命令失败({$code})：{$command}");
                }
                break;
            case TaskTypeEnum::URL['value']:
                // 连接失败时 file_get_contents 会抛 PHP 警告，失败信息由本方法统一记录
                if (@file_get_contents($command) === false) {
                    throw new RuntimeException("访问URL失败：{$command}");
                }
                break;
            case TaskTypeEnum::PHP['value']:
                eval($command);
                break;
            default:
                throw new RuntimeException('任务类型非法：' . (string) $model['type']);
        }
    }
}