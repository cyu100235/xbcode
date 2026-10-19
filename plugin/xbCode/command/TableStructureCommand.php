<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\command;

use plugin\xbCode\api\TableStructure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 检测并同步插件 install.sql 表结构到数据库
 *
 * 命令示例：
 *   php webman xb-table:sync                  全量检测并同步所有插件（含删除多余表）
 *   php webman xb-table:sync --dry-run        仅检测并输出差异SQL，不实际执行
 *   php webman xb-table:sync --plugin=xbUser  仅处理指定插件（不删除多余表）
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class TableStructureCommand extends Command
{
    /**
     * 命令名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultName = 'xb-table:sync';

    /**
     * 命令描述
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultDescription = '检测并同步插件install.sql表结构到数据库';

    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure()
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, '仅检测并输出差异SQL，不实际执行');
        $this->addOption('plugin', null, InputOption::VALUE_REQUIRED, '仅处理指定插件（插件目录名）', '');
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
        $dryRun = (bool)$input->getOption('dry-run');
        $plugin = (string)$input->getOption('plugin');

        try {
            $report = TableStructure::make()->run($dryRun, $plugin);
        } catch (\Throwable $th) {
            $output->writeln("<fg=red>表结构同步失败：{$th->getMessage()}</fg=red>");
            return self::FAILURE;
        }

        if ($dryRun) {
            $output->writeln('<fg=yellow>[DRY-RUN] 仅检测，不执行任何变更</fg=yellow>');
        }

        // 输出每个表的差异
        foreach ($report['tables'] as $table => $item) {
            $status = $item['status'];
            $output->writeln("<info>{$table}</info> [{$item['plugin']}] <comment>{$status}</comment>");
            foreach ($item['sql'] as $sql) {
                $output->writeln("    {$sql};");
            }
        }

        // 输出被删除的多余表
        foreach ($report['dropped_tables'] as $table) {
            $output->writeln("<fg=red>DROP TABLE {$table}</fg=red>");
        }

        // 输出错误
        foreach ($report['errors'] as $error) {
            $output->writeln("<fg=red>ERROR {$error}</fg=red>");
        }

        $summary = $report['summary'];
        $output->writeln(sprintf(
            '完成：新建 %d，同步 %d，无变化 %d，删除表 %d，错误 %d',
            $summary['created'],
            $summary['synced'],
            $summary['ok'],
            $summary['dropped_tables'],
            $summary['errors']
        ));

        // 单表错误不视为命令失败，避免中断部署流程
        return self::SUCCESS;
    }
}