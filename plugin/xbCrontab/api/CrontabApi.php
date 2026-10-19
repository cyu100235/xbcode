<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\api;

use Exception;
use support\Log;
use Webman\Event\Event;
use Brick\VarExporter\VarExporter;
use plugin\xbCrontab\app\model\Crontab;
use plugin\xbCrontab\app\model\CrontabLog;
use plugin\xbCrontab\app\process\XbCrontab;
use plugin\xbCrontab\api\CronExpressionApi;
use plugin\xbCrontab\app\validate\CrontabValidate;

/**
 * 定时任务接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabApi
{
    /**
     * 实例化
     * @return CrontabApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 添加定时任务
     * @param array $post
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function add(array $post)
    {
        xbValidate(CrontabValidate::class, $post, 'add');
        
        // 自动生成描述
        if (empty($post['cron_desc'])) {
            $post['cron_desc'] = CronExpressionApi::make()->parseCronExpression($post['cron_expression']);
        }
        
        $model = new Crontab;
        if (!$model->save($post)) {
            throw new Exception('添加定时任务失败');
        }
        $data = $model->toArray();
        // 发布定时任务更新事件
        ChannelClient::publish(XbCrontab::$eventName, $data);
        Event::dispatch('xbCrontab.Crontab.add', $data);
        return $data;
    }

    /**
     * 编辑定时任务
     * @param int $id
     * @param array $post
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function edit(int $id, array $post)
    {
        xbValidate(CrontabValidate::class, $post, 'edit');
        
        // 自动生成描述
        if (empty($post['cron_desc'])) {
            $post['cron_desc'] = CronExpressionApi::make()->parseCronExpression($post['cron_expression']);
        }
        
        $model = Crontab::find($id);
        if (!$model) {
            throw new Exception('定时任务不存在');
        }
        if (!$model->save($post)) {
            throw new Exception('编辑定时任务失败');
        }
        $data = $model->toArray();
        // 发布定时任务更新事件
        ChannelClient::publish(XbCrontab::$eventName, $data);
        Event::dispatch('xbCrontab.Crontab.edit', $data);
        return $data;
    }

    /**
     * 删除定时任务
     * @param int $id
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function del(int $id)
    {
        $model = Crontab::find($id);
        if (!$model) {
            throw new Exception('定时任务不存在');
        }
        $data = $model->toArray();
        if (!$model->delete()) {
            throw new Exception('删除定时任务失败');
        }
        // 发布定时任务更新事件
        ChannelClient::publish(XbCrontab::$eventName, $data);
        Event::dispatch('xbCrontab.Crontab.del', $data);
        return $data;
    }

    /**
     * 立即执行一次定时任务
     * @param Crontab $model 定时任务模型
     * @throws Exception
     * @return float 任务执行耗时(秒)
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function run(Crontab $model)
    {
        // 开始时间
        $startTime = microtime(true);
        try {
            // 获取任务执行类实例
            $taskApi = TaskApi::make();
            // 获取可执行方法列表
            $methods = $taskApi->getMethods();
            /**
             * 获取任务类型
             * @var string
             */
            $type = $model['type'];
            if (!isset($methods[$type])) {
                throw new Exception("任务类型:{$type}，不存在");
            }
            // 调用任务执行方法
            $method = $methods[$type];
            $result = $taskApi->$method($model['command']);
            // 记录任务耗时(秒)
            $runSecondTime = microtime(true) - $startTime;
            // 更新数据
            $model->save([
                'last_time' => date('Y-m-d H:i:s'),
                'error' => '',
            ]);
            // 记录执行日志（含执行目标的返回结果）
            $this->saveLog((int)$model['id'], $runSecondTime, $this->formatRemark($result));
            return $runSecondTime;
        } catch (\Throwable $th) {
            // 记录任务耗时(秒)
            $runSecondTime = microtime(true) - $startTime;
            // 更新数据
            $model->save([
                'last_time' => date('Y-m-d H:i:s'),
                'state' => '30',
                'error' => $th->getMessage(),
            ]);
            // 记录执行日志
            $this->saveLog((int)$model['id'], $runSecondTime, $th->getMessage());
            throw new Exception($th->getMessage());
        }
    }

    /**
     * 记录任务执行日志
     * @param int $crontabId 定时任务ID
     * @param float $runSecondTime 执行耗时(秒)
     * @param string $remarks 执行备注
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function saveLog(int $crontabId, float $runSecondTime, string $remarks)
    {
        $logModel = new CrontabLog;
        $logModel->save([
            'crontab_id' => $crontabId,
            'run_second_time' => $runSecondTime,
            'remarks' => $remarks,
        ]);
    }

    /**
     * 格式化执行结果备注
     * @param mixed $result 任务执行返回结果
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function formatRemark($result): string
    {
        $result = is_string($result) ? trim($result) : '';
        // 无返回结果时使用默认备注
        if ($result === '') {
            return '任务执行完成';
        }
        return '任务执行完成：' . $result;
    }

    /**
     * 导出定时任务
     * @param int $crontabIds 定时任务ID
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function exportCrontab(array $crontabIds)
    {
        $data = Crontab::whereIn('id', $crontabIds)->select()->toArray();
        if (empty($data)) {
            throw new Exception('定时任务不存在');
        }
        $crontabs = [];
        foreach ($data as $value) {
            $path = base_path("plugin/{$value['plugin']}/config/crontab.php");
            $crontab = [];
            if (file_exists($path)) {
                $crontab = include $path;
            }
            $crontab = array_column($crontab, null, 'name');
            $crontab[$value['name']] = [
                'title' => $value['title'],
                'name' => $value['name'],
                'plugin' => $value['plugin'],
                'type' => $value['type'],
                'cron_expression' => $value['cron_expression'],
                'cron_desc' => $value['cron_desc'],
                'command' => $value['command'],
            ];
            // 重设索引
            $crontab = array_values($crontab);
            $commit = <<<PHP
            /**
             * 定时任务配置文件
             * title 任务名称
             * name 任务标识
             * plugin 所属插件
             * type 任务类型
             * cron_expression Cron表达式
             * cron_desc 周期描述
             * command 执行命令
             */
            PHP;
            $content = VarExporter::export($crontab);
            $content = "<?php\n{$commit}\nreturn {$content};\n";
            // 保存文件
            file_put_contents($path, $content);
        }
    }

    /**
     * 导出插件旗下所有定时任务
     * @param string $name 插件标识
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function exportPlugin(string $name)
    {
        $data = Crontab::where('plugin', $name)->column('id');
        $this->exportCrontab($data);
    }
    
    /**
     * 获取定时任务执行描述
     * @param string $cronDesc Cron描述
     * @param string $cronExpression Cron表达式
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getRunDesc(string $cronDesc, string $cronExpression = '')
    {
        // 优先使用描述
        if (!empty($cronDesc)) {
            return $cronDesc;
        }
        // 如果没有描述，从表达式解析
        return CronExpressionApi::make()->parseCronExpression($cronExpression);
    }

    /**
     * 获取插件定时任务配置
     * @param string $pluginName
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getPluginCrontab(string $pluginName)
    {
        $configPath = base_path("/plugin/{$pluginName}/config/crontab.php");
        if (!file_exists($configPath)) {
            return [];
        }
        $data = include $configPath;
        if (empty($data)) {
            return [];
        }
        return $data;
    }

    /**
     * 安装插件定时任务
     * @param string $pluginName
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function install(string $pluginName)
    {
        $data = $this->getPluginCrontab($pluginName);
        foreach ($data as $value) {
            if (!is_array($value)) {
                continue;
            }
            $value['state'] = '20';
            $value['plugin'] = $pluginName;
            // 确保有描述
            if (empty($value['cron_desc'])) {
                $value['cron_desc'] = CronExpressionApi::make()->parseCronExpression($value['cron_expression'] ?? '');
            }
            try {
                $this->add($value);
            } catch (\Throwable $th) {
                Log::error("安装定时任务出错：{$th->getMessage()}");
            }
        }
    }

    /**
     * 卸载定时任务
     * @param string $pluginName
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function uninstall(string $pluginName)
    {
        return Crontab::where('plugin', $pluginName)->delete();
    }
}
