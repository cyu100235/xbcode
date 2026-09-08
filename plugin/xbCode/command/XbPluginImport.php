<?php
/**
 * 插件数据导入命令
 *
 * 把 webman 插件目录下「由 xb:plugin:export 导出的 4 份产物」反向导入本地系统：
 *   - install.sql       → 数据库（Mysql::importSql，前缀 xb_ → 项目表前缀）
 *   - config/menu.php   → xb_admin_rule 表（按 plugin + path upsert）
 *   - config/crontab.php→ 预留（xbCrontab 加载后扩展）
 *   - config/dict.php   → 预留（xbDict 加载后扩展）
 *
 * 与 xb:plugin:export 对称；参数、选项、组合方式保持一致：
 *
 * 用法示例：
 *   # 导入全部（表结构 + 菜单 + 定时任务 + 字典）
 *   php webman xb:plugin:import xbCrontab
 *
 *   # 只导入菜单（--type 或 --t）
 *   php webman xb:plugin:import xbCrontab --type=menu
 *   php webman xb:plugin:import xbCrontab --t=menu
 *
 *   # 只导入表结构
 *   php webman xb:plugin:import xbCrontab --type=sql
 *   php webman xb:plugin:import xbCrontab --t=sql
 *
 *   # 只导入定时任务（预留）
 *   php webman xb:plugin:import xbCrontab --type=crontab
 *
 *   # 只导入字典（预留）
 *   php webman xb:plugin:import xbCrontab --type=dict
 *
 *   # 多类型组合（逗号分割）
 *   php webman xb:plugin:import xbCrontab --type=sql,menu
 *   php webman xb:plugin:import xbCrontab --t=sql,menu
 *
 *   # 菜单归属 plugin 字段与插件名不同时（例如把 xbCode 导出的 menu 菜单快照写入 xbAdmin 归属）
 *   php webman xb:plugin:import xbCode --type=menu --menu-plugin=xbAdmin
 *
 * 参数说明：
 *   plugin        必填，插件名（如 xbCode、xbCrontab）
 *   --type        导入类型（sql/menu/crontab/dict，逗号分割，留空=全部）
 *   --t           --type 简写
 *   --menu-plugin 写入 xb_admin_rule.plugin 的值（默认同 plugin 参数）
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */

namespace plugin\xbCode\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use plugin\xbCode\api\PluginImport;

/**
 * 插件数据导入命令
 *
 * 分步执行 sql/menu/crontab/dict 四种导入；单步失败不中断后续步骤，但最终整体退出码按失败集判定。
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
#[AsCommand('xb:plugin:import', '导入插件数据（表结构/菜单/定时任务/字典）')]
class XbPluginImport extends Command
{
    /**
     * 命令参数与选项定义
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure(): void
    {
        $this
            ->addArgument('plugin', InputArgument::REQUIRED, '插件名（如 xbCode、xbCrontab）')
            ->addOption('type', null, InputOption::VALUE_OPTIONAL, '导入类型（sql/menu/crontab/dict，逗号分割，留空=全部）', '')
            ->addOption('t', null, InputOption::VALUE_OPTIONAL, '导入类型简写（同 --type）', '')
            ->addOption('menu-plugin', null, InputOption::VALUE_OPTIONAL, '菜单归属插件名（写入 xb_admin_rule.plugin 字段，默认同 plugin 参数）', '');
    }

    /**
     * 执行导入
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
        if ($menuPlugin === '') {
            $menuPlugin = $plugin;
        }

        // 合并 --type 和 --t
        $type = (string)$input->getOption('type');
        if ($type === '') {
            $type = (string)$input->getOption('t');
        }
        $onlyList = $type !== '' ? array_values(array_filter(explode(',', $type), static fn($v) => trim((string)$v) !== '')) : [];
        $onlyList = array_map(static fn($v) => trim((string)$v), $onlyList);
        $doAll    = empty($onlyList);

        $supported = ['sql', 'menu', 'crontab', 'dict'];
        if (!$doAll) {
            $invalid = array_diff($onlyList, $supported);
            if ($invalid !== []) {
                $output->writeln('<error>不支持的 --type 值：' . implode(', ', $invalid) . '</error>');
                $output->writeln('<comment>支持：sql / menu / crontab / dict（逗号分割，留空=全部）</comment>');
                return self::FAILURE;
            }
        }

        $output->writeln('<info>========== 插件数据导入 ==========</info>');
        $output->writeln('<info>插件名: ' . $plugin . '</info>');
        if ($menuPlugin !== $plugin) {
            $output->writeln('<info>菜单归属(plugin): ' . $menuPlugin . '</info>');
        }
        if ($onlyList !== []) {
            $output->writeln('<info>导入类型: ' . implode(', ', $onlyList) . '</info>');
        } else {
            $output->writeln('<info>导入类型: 全部（sql, menu, crontab, dict）</info>');
        }
        $output->writeln('');

        $failed = 0;
        $steps  = [
            'sql'     => ['label' => '表结构 install.sql', 'call' => static function () use ($plugin) { return PluginImport::importSql($plugin); }],
            'menu'    => ['label' => '菜单 config/menu.php', 'call' => static function () use ($plugin, $menuPlugin) { return PluginImport::importMenus($plugin, $menuPlugin); }],
            'crontab' => ['label' => '定时任务 config/crontab.php', 'call' => static function () use ($plugin) { return PluginImport::importCrontab($plugin); }],
            'dict'    => ['label' => '字典 config/dict.php',       'call' => static function () use ($plugin) { return PluginImport::importDict($plugin); }],
        ];

        foreach ($steps as $key => $step) {
            if (!$doAll && !in_array($key, $onlyList, true)) {
                continue;
            }
            $output->writeln('<info>--- 导入 ' . $step['label'] . ' ---</info>');
            try {
                $result = ($step['call'])();
                if ($result === null) {
                    $output->writeln('  <comment>预留功能，已跳过（对应插件未启用）</comment>');
                } else {
                    $output->writeln("  <info>完成 {$result} 项</info>");
                }
            } catch (\Throwable $e) {
                $failed++;
                $output->writeln('  <error>失败：' . $e->getMessage() . '</error>');
            }
        }

        $output->writeln('<info>========== 导入完成 ==========</info>');
        if ($failed > 0) {
            $output->writeln("<comment>共失败 {$failed} 步</comment>");
            return self::FAILURE;
        }
        return self::SUCCESS;
    }
}