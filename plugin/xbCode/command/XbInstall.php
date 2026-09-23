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

use plugin\xbCode\api\XbCode;
use RuntimeException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('xb:install', '批量安装所有插件')]
class XbInstall extends Command
{
    /**
     * 命令配置
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure(): void
    {
    }

    /**
     * 执行安装：扫描 plugin 目录并批量安装
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>========== xb:install 批量安装插件 ==========</info>');
        $output->writeln('');

        $basePath = $this->getBasePath();
        $pluginDir = $basePath . '/plugin';
        if (!is_dir($pluginDir)) {
            $output->writeln('<error>插件目录不存在：' . $pluginDir . '</error>');
            return self::FAILURE;
        }

        // 检查框架是否已初始化
        XbCode::checkEnv();
        // 检查数据库连接是否正常
        XbCode::checkDatabase();

        $plugins = $this->collectPluginNames($pluginDir);
        if ($plugins === []) {
            $output->writeln('<comment>未发现任何可安装的插件（需同时存在 api/Install.php 与 plugin.json）。</comment>');
            return self::SUCCESS;
        }

        $ordered = $this->orderPlugins($plugins);
        $total   = count($ordered);
        $output->writeln("<comment>共发现 {$total} 个插件，安装顺序：" . implode(', ', $ordered) . '</comment>');
        $output->writeln('');

        $pass = 0;
        $fail = 0;
        $summary = [];

        foreach ($ordered as $name) {
            $output->writeln("<question>[{($pass + $fail + 1)}/{$total}] 安装插件: {$name}</question>");
            try {
                $exitCode = $this->runOfficialInstallCommand($name, $output);
            } catch (\Throwable $e) {
                $output->writeln('<error>未捕获异常: ' . $e->getMessage() . '</error>');
                $exitCode = self::FAILURE;
            }
            if ($exitCode === self::SUCCESS) {
                $pass++;
                $summary[] = "  <info>PASS</info> {$name}";
            } else {
                $fail++;
                $summary[] = "  <error>FAIL</error> {$name} (exit={$exitCode})";
            }
            $output->writeln('');
        }

        $output->writeln('<info>========== 安装汇总 ==========</info>');
        foreach ($summary as $line) {
            $output->writeln($line);
        }
        $output->writeln("<comment>共 {$total} 个，成功 {$pass}，失败 {$fail}</comment>");
        return $fail === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * 执行官方 app-plugin:install <name>
     * 优先使用当前 Symfony\Application 复用命令注册表，找不到时 fallback 到命令行 `php webman app-plugin:install <name>`
     * @param string $name
     * @param OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function runOfficialInstallCommand(string $name, OutputInterface $output): int
    {
        $command = $this->getApplication()?->find('app-plugin:install');
        if ($command !== null) {
            $installInput = new ArrayInput(['command' => 'app-plugin:install', 'name' => $name]);
            return $command->run($installInput, $output);
        }

        // Fallback: 通过外部 CLI 调用
        $output->writeln('<comment>未找到 app-plugin:install 命令注册表，改用 CLI: php webman app-plugin:install ' . $name . '</comment>');
        $cmd = $this->buildCliCommand($name);
        $output->writeln($cmd);
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $proc = proc_open($cmd, $descriptors, $pipes, $this->getBasePath());
        if (!is_resource($proc)) {
            $output->writeln('<error>无法启动子进程: ' . $cmd . '</error>');
            return self::FAILURE;
        }
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = (int)proc_close($proc);
        if (trim($stdout) !== '') {
            $output->writeln($stdout);
        }
        if (trim($stderr) !== '') {
            $output->writeln('<error>' . $stderr . '</error>');
        }
        return $exitCode === 0 ? self::SUCCESS : $exitCode;
    }

    /**
     * 把 xbCode 固定为第一个安装，其余插件按目录名字典序排列，保证核心插件依赖顺序稳定
     * 除 xbCode 外不做任何硬编码优先级，避免后续新增插件（如 xbApi 等）排序被写死
     * @param array<int,string> $names
     * @return array<int,string>
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function orderPlugins(array $names): array
    {
        $isCore = 'xbCode';
        $rest   = [];
        $hasCore = false;
        foreach ($names as $n) {
            if ($n === $isCore) {
                $hasCore = true;
                continue;
            }
            $rest[] = $n;
        }
        sort($rest, SORT_STRING);
        return $hasCore ? array_merge([$isCore], $rest) : $rest;
    }

    /**
     * 收集所有合格的插件目录名
     * 合格条件：存在 api/Install.php 并且 存在 plugin.json
     * @param string $pluginDir
     * @return array<int,string>
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function collectPluginNames(string $pluginDir): array
    {
        $dirs = glob($pluginDir . '/*', GLOB_ONLYDIR);
        if ($dirs === false) {
            return [];
        }
        $names = [];
        foreach ($dirs as $dir) {
            $name = basename($dir);
            if ($name === '' || $name === '.' || $name === '..') {
                continue;
            }
            if (!is_file($dir . '/api/Install.php')) {
                continue;
            }
            if (!is_file($dir . '/plugin.json')) {
                continue;
            }
            $names[] = $name;
        }
        return $names;
    }

    /**
     * 构建跨平台的外部 CLI 命令
     * @param string $name
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function buildCliCommand(string $name): string
    {
        $phpBinary = PHP_BINARY;
        $basePath  = $this->getBasePath();
        $webman    = $basePath . '/webman';
        $safeWebman = escapeshellarg($webman);
        $safeName   = escapeshellarg($name);
        $windows    = DIRECTORY_SEPARATOR === '\\';
        $command    = "{$phpBinary} {$safeWebman} app-plugin:install {$safeName}";
        if ($windows) {
            return $command;
        }
        return $command;
    }

    /**
     * 获取项目根目录
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getBasePath(): string
    {
        if (function_exists('base_path')) {
            return rtrim(str_replace('\\', '/', base_path()), '/');
        }
        return rtrim(str_replace('\\', '/', dirname(__DIR__, 3)), '/');
    }
}