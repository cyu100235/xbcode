<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\command;

use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCrontab\api\CrontabApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 同步插件定时任务配置
 *
 * 按各插件 config/crontab.php 增量同步：缺失的补齐，已有的更新配置，
 * 保留后台手工创建的任务、任务状态与历史执行日志。
 *
 * 命令示例：
 *   php webman xb-crontab:sync                同步所有已启用插件的定时任务
 *   php webman xb-crontab:sync --plugin=xbDevice  仅同步指定插件
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class CrontabSyncCommand extends Command
{
    /**
     * 命令名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultName = 'xb-crontab:sync';

    /**
     * 命令描述
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultDescription = '同步插件定时任务配置（增量补充与更新）';

    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure()
    {
        $this->addOption('plugin', null, InputOption::VALUE_REQUIRED, '仅同步指定插件（插件目录名）', '');
    }

    /**
     * 执行命令
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $plugin = (string)$input->getOption('plugin');

        // 定时任务插件未安装或数据表缺失时无法同步
        if (!Mysql::hasTable('crontab')) {
            $output->writeln('<fg=yellow>未检测到定时任务数据表，跳过定时任务同步</fg=yellow>');
            return self::SUCCESS;
        }

        // 待同步的插件列表
        if ($plugin !== '') {
            $plugins = [$plugin];
        } else {
            $plugins = array_column(PluginsApi::make()->getEnabledList(), 'name');
        }

        $summary = ['created' => 0, 'updated' => 0, 'unchanged' => 0];
        $failed = [];
        foreach ($plugins as $name) {
            $result = CrontabApi::make()->sync($name);
            $summary['created'] += $result['created'];
            $summary['updated'] += $result['updated'];
            $summary['unchanged'] += $result['unchanged'];
            foreach ($result['failed'] as $error) {
                $failed[] = "{$name} {$error}";
            }
        }

        foreach ($failed as $error) {
            $output->writeln("<fg=red>ERROR {$error}</fg=red>");
        }
        $output->writeln(sprintf(
            '完成：新建 %d，更新 %d，无变化 %d，失败 %d',
            $summary['created'],
            $summary['updated'],
            $summary['unchanged'],
            count($failed)
        ));

        // 单条任务失败不视为命令失败，避免中断部署流程
        return self::SUCCESS;
    }
}
