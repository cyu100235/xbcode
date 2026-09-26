<?php
namespace plugin\xbCrontab\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 系统文件日志清理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class SystemLogCommand extends Command
{
    /**
     * 执行命令
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultName = 'file:clear:log';

    /**
     * 任务描述
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultDescription = 'Xb File Clear Log';

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
        // 处理系统文件日志清理
        $this->checkSystemFileLog();
        // 任务处理完成
        $output->writeln("<fg=green>成功清理系统文件日志...</fg=green>");
        // 返回完成
        return self::SUCCESS;
    }

    /**
     * 清理系统文件日志
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function checkSystemFileLog()
    {
        $logPath = runtime_path('logs');
        if (!is_dir($logPath)) {
            return;
        }
        $files = glob($logPath . '/*.log');
        if (empty($files)) {
            return;
        }
        foreach ($files as $path) {
            unlink($path);
            $this->output->writeln("<fg=green>成功清理文件:{$path}...</fg=green>");
        }
    }
}
