<?php
namespace plugin\xbCrontab\command;

use plugin\xbCrontab\app\model\CrontabLog;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 清理定时任务日志
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabLogCommand extends Command
{
    /**
     * 执行命令
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultName = 'crontab:clear:log';

    /**
     * 任务描述
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultDescription = 'Xb Crontab Clear Log';

    /**
     * 输入参数
     * @var InputInterface
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected InputInterface $input;

    /**
     * 输出参数
     * @var OutputInterface
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected OutputInterface $output;

    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure()
    {
        // $this->addArgument('name', InputArgument::REQUIRED, 'Xb plugin name');
    }

    /**
     * 执行命令
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 注入输入输出参数
        $this->input = $input;
        $this->output = $output;
        // 处理清理定时任务日志
        $this->checkCrontabLog();
        // 任务处理完成
        $output->writeln("<fg=green>成功清理定时任务日志...</fg=green>");
        // 返回完成
        return self::SUCCESS;
    }

    /**
     * 清理定时任务日志
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function checkCrontabLog()
    {
        $data = CrontabLog::select()->toArray();
        if (empty($data)) {
            return;
        }
        foreach ($data as $item) {
            $model = CrontabLog::find($item['id']);
            if (!$model) {
                continue;
            }
            if (!$model->delete()) {
                $this->output->writeln("<fg=red>清理定时任务日志失败：{$item['id']}...</fg=red>");
                continue;
            }
            $this->output->writeln("<fg=green>成功清理定时任务日志：{$item['id']}...</fg=green>");
        }
    }
}
