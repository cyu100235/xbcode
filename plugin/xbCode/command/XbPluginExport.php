<?php
/**
 * 插件数据导出命令
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */

namespace plugin\xbCode\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use plugin\xbCode\api\PluginExport;

/**
 * 插件数据导出
 * 支持：表结构 -> install.sql、菜单 -> config/menu.php、定时任务 -> config/crontab.php（预留）、字典 -> config/dict.php（预留）
 *
 * 用法示例：
 *   # 导出全部（表结构 + 菜单 + 定时任务 + 字典）
 *   php webman xb:plugin:export xbCode
 *
 *   # 只导出菜单（--type 或 --t）
 *   php webman xb:plugin:export xbCode --type=menu
 *   php webman xb:plugin:export xbCode --t=menu
 *
 *   # 只导出表结构
 *   php webman xb:plugin:export xbCode --type=sql
 *   php webman xb:plugin:export xbCode --t=sql
 *
 *   # 只导出定时任务（预留）
 *   php webman xb:plugin:export xbCode --type=crontab
 *   php webman xb:plugin:export xbCode --t=crontab
 *
 *   # 只导出字典（预留）
 *   php webman xb:plugin:export xbCode --type=dict
 *   php webman xb:plugin:export xbCode --t=dict
 *
 *   # 多类型组合（逗号分割）
 *   php webman xb:plugin:export xbCode --type=sql,menu
 *   php webman xb:plugin:export xbCode --t=sql,menu
 *
 *   # 菜单 plugin 字段与插件名不同时（如菜单归属 xbAdmin 但导出到 xbCode）
 *   php webman xb:plugin:export xbCode --type=menu --menu-plugin=xbAdmin
 *
 * 参数说明：
 *   plugin        必填，插件名（如 xbCode、xbUpload）
 *   --type        导出类型（sql/menu/crontab/dict，逗号分割，留空=全部）
 *   --t           --type 简写
 *   --menu-plugin 菜单数据归属插件名（数据库 plugin 字段，默认同 plugin 参数）
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
#[AsCommand('xb:plugin:export', '导出插件数据（表结构/菜单/定时任务/字典）')]
class XbPluginExport extends Command
{
    /**
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure(): void
    {
        $this
            ->addArgument('plugin', InputArgument::REQUIRED, '插件名（如 xbCode）')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, '导出类型（sql/menu/crontab/dict，逗号分割，留空=全部）', '')
            ->addOption('t', null, InputOption::VALUE_OPTIONAL, '导出类型简写（同 --type）', '')
            ->addOption('menu-plugin', null, InputOption::VALUE_OPTIONAL, '菜单数据归属插件名（数据库 plugin 字段，默认同 plugin 参数）', '');
    }

    /**
     * 执行导出
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $plugin     = (string)$input->getArgument('plugin');
        $menuPlugin = (string)$input->getOption('menu-plugin');
        if ($menuPlugin === '') { $menuPlugin = $plugin; }

        // 合并 --type 和 --t
        $type = (string)$input->getOption('type');
        if ($type === '') { $type = (string)$input->getOption('t'); }
        $onlyList = $type !== '' ? array_filter(explode(',', $type), fn($v) => $v !== '') : [];
        $doAll    = empty($onlyList);

        $output->writeln('<info>========== 插件数据导出 ==========</info>');
        $output->writeln('<info>插件名: ' . $plugin . '</info>');
        if ($menuPlugin !== $plugin) {
            $output->writeln('<info>菜单归属: ' . $menuPlugin . '</info>');
        }

        // 1. 导出表结构
        if ($doAll || in_array('sql', $onlyList, true)) {
            $output->writeln('<info>--- 导出表结构 ---</info>');
            try {
                $count = PluginExport::exportSql($plugin);
                $output->writeln("  <info>导出 {$count} 张表结构</info>");
            } catch (\Throwable $e) {
                $output->writeln("  <error>{$e->getMessage()}</error>");
            }
        }

        // 2. 导出菜单
        if ($doAll || in_array('menu', $onlyList, true)) {
            $output->writeln('<info>--- 导出菜单 ---</info>');
            try {
                $count = PluginExport::exportMenus($plugin, $menuPlugin);
                $output->writeln("  <info>导出 {$count} 条顶级菜单</info>");
            } catch (\Throwable $e) {
                $output->writeln("  <error>{$e->getMessage()}</error>");
            }
        }

        // 3. 导出定时任务
        if ($doAll || in_array('crontab', $onlyList, true)) {
            $output->writeln('<info>--- 导出定时任务 ---</info>');
            try {
                $r = PluginExport::exportCrontab($plugin);
                if ($r === null) {
                    $output->writeln("  <comment>预留功能，已跳过</comment>");
                } else {
                    $output->writeln("  <info>导出 {$r} 条定时任务</info>");
                }
            } catch (\Throwable $e) {
                $output->writeln("  <error>{$e->getMessage()}</error>");
            }
        }

        // 4. 导出字典
        if ($doAll || in_array('dict', $onlyList, true)) {
            $output->writeln('<info>--- 导出字典 ---</info>');
            try {
                $r = PluginExport::exportDict($plugin);
                if ($r === null) {
                    $output->writeln("  <comment>预留功能，已跳过</comment>");
                } else {
                    $output->writeln("  <info>导出 {$r} 条字典</info>");
                }
            } catch (\Throwable $e) {
                $output->writeln("  <error>{$e->getMessage()}</error>");
            }
        }

        $output->writeln('<info>========== 导出完成 ==========</info>');
        return self::SUCCESS;
    }
}