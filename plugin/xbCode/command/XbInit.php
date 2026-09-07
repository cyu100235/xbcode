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

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('xb:init', '初始化并配置xbCode框架')]
class XbInit extends Command
{
    /**
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure(): void
    {
        $this
            ->setDescription('初始化并配置xbCode框架')
            // 设置数据库配置
            ->addOption('db-host', null, InputOption::VALUE_OPTIONAL, '数据库地址（留空则交互询问）')
            ->addOption('db-port', null, InputOption::VALUE_OPTIONAL, '数据库端口（留空则交互询问）')
            ->addOption('db-name', null, InputOption::VALUE_OPTIONAL, '数据库名（留空则交互询问）')
            ->addOption('db-user', null, InputOption::VALUE_OPTIONAL, '数据库用户（留空则交互询问）')
            ->addOption('db-pass', null, InputOption::VALUE_OPTIONAL, '数据库密码（留空则交互询问或为空）', '')
            ->addOption('db-prefix', null, InputOption::VALUE_OPTIONAL, '数据库表前缀（留空则交互询问）')
            // 设置Redis配置
            ->addOption('redis-host', null, InputOption::VALUE_OPTIONAL, 'Redis 地址（留空则交互询问）')
            ->addOption('redis-port', null, InputOption::VALUE_OPTIONAL, 'Redis 端口（留空则交互询问）')
            ->addOption('redis-pass', null, InputOption::VALUE_OPTIONAL, 'Redis 密码（留空则交互询问或为空）', '')
            ->addOption('cache-type', null, InputOption::VALUE_OPTIONAL, '缓存类型：redis / redis_cluster / file（留空则交互询问）')
            ->addOption('redis-prefix', null, InputOption::VALUE_OPTIONAL, 'Redis 前缀（留空则交互询问）')
            // 设置项目监听端口
            ->addOption('listen-port', null, InputOption::VALUE_OPTIONAL, '项目监听端口（留空则交互询问）');
    }

    /**
     * 执行安装命令
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $basePath = $this->getBasePath();
        $xbCodeDir = rtrim(str_replace('\\', '/', dirname(__DIR__)));

        $output->writeln('<info>========== XbCode 项目安装向导 ==========</info>');
        $output->writeln('');

        // ========== 预检 ==========
        if ($this->preCheckPluginFiles($xbCodeDir, $output) !== self::SUCCESS) {
            return self::FAILURE;
        }

        try {
            // ========== 步骤1 数据库配置 ==========
            [
                'db_host' => $dbHost,
                'db_port' => $dbPort,
                'db_name' => $dbName,
                'db_user' => $dbUser,
                'db_pass' => $dbPass,
                'db_prefix' => $dbPrefix,
            ] = $this->askDatabaseConfig($helper, $input, $output);

            // ========== 步骤2 Redis配置 ==========
            [
                'redis_host' => $redisHost,
                'redis_port' => $redisPort,
                'redis_pass' => $redisPass,
                'cache_type' => $cacheType,
                'redis_prefix' => $redisPrefix,
            ] = $this->askRedisConfig($helper, $input, $output);

            // ========== 步骤3 服务端口配置 ==========
            $listenPort = $this->askListenPort($helper, $input, $output);
        } catch (RuntimeException $e) {
            // 所有 CLI 参数校验失败 -> 抛出 RuntimeException，已打印错误，返回非 0
            return self::FAILURE;
        }

        // ========== 步骤4 复制配置文件 ==========
        $this->writeConfigPhp($basePath, $listenPort, $output);

        // ========== 步骤5 生成 nginx.conf ==========
        $this->writeNginxConf($basePath, $listenPort, $output);

        // ========== 步骤6 写入 .env 配置文件 ==========
        $this->writeEnvFile($basePath, [
            'db_host' => $dbHost,
            'db_name' => $dbName,
            'db_user' => $dbUser,
            'db_pass' => $dbPass,
            'db_port' => $dbPort,
            'db_prefix' => $dbPrefix,
            'cache_type' => $cacheType,
            'redis_host' => $redisHost,
            'redis_port' => $redisPort,
            'redis_pass' => $redisPass,
            'redis_prefix' => $redisPrefix,
        ], $output);

        // ========== 完成提示 ==========
        $this->printComplete($basePath, $listenPort, $output);

        return self::SUCCESS;
    }

    /**
     * 获取命令行选项（对齐一键安装）
     * - 用户显式传了该选项（通过 hasParameterOption 检测）时返回其值
     * - 没传时返回 null，由调用方继续走交互分支
     * @param InputInterface $input
     * @param string $name        短横线风格的选项名，例如 "db-host"
     * @param mixed  $default
     * @param bool   $isNumber    是否按数值语义（trim + 不能为空）
     * @param bool   $requireNonEmpty 非空时才视为"用户提供了值"（true 用于 db-host 这类必须有内容的选项；false 用于密码允许传空字符串）
     * @return mixed
     */
    protected function optionOrDefault(InputInterface $input, string $name, mixed $default = null, bool $isNumber = false, bool $requireNonEmpty = true): mixed
    {
        $cliName = '--' . $name;
        if (!$input->hasParameterOption($cliName, true)) {
            return $default;
        }
        $val = $input->getOption($name);
        if ($val === null) {
            return $requireNonEmpty ? $default : '';
        }
        if (is_string($val)) {
            $val = trim($val);
        }
        if ($requireNonEmpty && $val === '') {
            return $default;
        }
        if ($isNumber && is_string($val) && !ctype_digit($val)) {
            // 端口等数值：格式非法时仍返回字符串，后续 validatePort 会报错；这里只做空值处理
            return $val === '' ? $default : $val;
        }
        return $val;
    }

    /**
     * 校验端口号是否合法（1-65535）
     * @param mixed $val
     * @param string|null $msg 输出错误原因
     * @return bool
     */
    protected function validatePort(mixed $val, ?string &$msg = null): bool
    {
        if ($val === null || (is_string($val) && trim($val) === '')) {
            $msg = '不能为空';
            return false;
        }
        $str = is_string($val) ? trim($val) : (string) $val;
        if (!ctype_digit($str)) {
            $msg = "必须是纯数字，收到 \"{$str}\"";
            return false;
        }
        $num = (int) $str;
        if ($num < 1 || $num > 65535) {
            $msg = "必须在 1-65535 之间，收到 {$num}";
            return false;
        }
        $msg = null;
        return true;
    }

    /**
     * 预检 xbCode 插件的 plugin.json / preview.svg 是否存在
     * @param string $xbCodeDir
     * @param OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function preCheckPluginFiles(string $xbCodeDir, OutputInterface $output): int
    {
        $pluginFile = $xbCodeDir . DIRECTORY_SEPARATOR . 'plugin.json';
        $previewFile = $xbCodeDir . DIRECTORY_SEPARATOR . 'preview.svg';
        $missing = [];
        if (!is_file($pluginFile)) {
            $missing[] = '  - <error>' . str_replace('\\', '/', $pluginFile) . '  不存在</error>';
        }
        if (!is_file($previewFile)) {
            $missing[] = '  - <error>' . str_replace('\\', '/', $previewFile) . '  不存在</error>';
        }
        if ($missing !== []) {
            $output->writeln('<comment>[预检] 检测到以下核心文件缺失，缺少它们将导致插件安装与展示无法继续：</comment>');
            foreach ($missing as $line) {
                $output->writeln($line);
            }
            $output->writeln('<comment>请补齐后重新运行安装向导。</comment>');
            return self::FAILURE;
        }
        $output->writeln('<info>[预检] 自动预检已完成，准备开始安装...</info>');
        $output->writeln('');
        return self::SUCCESS;
    }

    /**
     * 步骤 1：询问数据库配置
     * @param mixed $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array{db_host:string, db_port:string, db_name:string, db_user:string, db_pass:string, db_prefix:string}
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function askDatabaseConfig($helper, InputInterface $input, OutputInterface $output): array
    {
        $output->writeln('<comment>[1/3] 数据库配置</comment>');
        $dbHost = $this->optionOrDefault($input, 'db-host', null, false, true) ?? (string) $helper->ask($input, $output, new Question('请输入数据库地址 [127.0.0.1]: ', '127.0.0.1'));
        $dbPort = $this->optionOrDefault($input, 'db-port', null, true, true) ?? (string) $helper->ask($input, $output, new Question('请输入数据库端口 [3306]: ', '3306'));
        $dbName = $this->optionOrDefault($input, 'db-name', null, false, true) ?? (string) $helper->ask($input, $output, new Question('请输入数据库名 [xb_code]: ', 'xb_code'));
        $dbUser = $this->optionOrDefault($input, 'db-user', null, false, true) ?? (string) $helper->ask($input, $output, new Question('请输入数据库用户 [root]: ', 'root'));
        $dbPass = $this->optionOrDefault($input, 'db-pass', null, false, false) ?? (string) $helper->ask($input, $output, new Question('请输入数据库密码 (直接回车留空): ', ''));
        $dbPrefix = $this->optionOrDefault($input, 'db-prefix', null, false, true) ?? (string) $helper->ask($input, $output, new Question('请输入数据库表前缀 [xb_]: ', 'xb_'));

        if (!$this->validatePort($dbPort, $msg)) {
            $output->writeln("<error>参数 --db-port 非法：{$msg}</error>");
            throw new RuntimeException('参数校验失败');
        }
        $output->writeln('');
        return [
            'db_host' => (string) $dbHost,
            'db_port' => (string) $dbPort,
            'db_name' => (string) $dbName,
            'db_user' => (string) $dbUser,
            'db_pass' => (string) $dbPass,
            'db_prefix' => (string) $dbPrefix,
        ];
    }

    /**
     * 步骤 2：询问 Redis 配置
     * @param mixed $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array{redis_host:string, redis_port:string, redis_pass:string, cache_type:string, redis_prefix:string}
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function askRedisConfig($helper, InputInterface $input, OutputInterface $output): array
    {
        $output->writeln('<comment>[2/3] Redis配置</comment>');
        $redisHost = $this->optionOrDefault($input, 'redis-host', null, false, true) ?? (string) $helper->ask($input, $output, new Question('请输入Redis地址 [127.0.0.1]: ', '127.0.0.1'));
        $redisPort = $this->optionOrDefault($input, 'redis-port', null, true, true) ?? (string) $helper->ask($input, $output, new Question('请输入Redis端口 [6379]: ', '6379'));
        $redisPass = $this->optionOrDefault($input, 'redis-pass', null, false, false) ?? (string) $helper->ask($input, $output, new Question('请输入Redis密码 (直接回车留空): ', ''));
        $cacheType = $this->optionOrDefault($input, 'cache-type', null, false, true) ?? (string) $helper->ask($input, $output, new Question('请输入缓存类型 [redis]: ', 'redis'));
        $redisPrefix = $this->optionOrDefault($input, 'redis-prefix', null, false, true) ?? (string) $helper->ask($input, $output, new Question('请输入Redis前缀 [xb_cache_]: ', 'xb_cache_'));

        if (!$this->validatePort($redisPort, $msg)) {
            $output->writeln("<error>参数 --redis-port 非法：{$msg}</error>");
            throw new RuntimeException('参数校验失败');
        }
        $cacheType = strtolower((string) $cacheType);
        if (!in_array($cacheType, ['redis', 'redis_cluster', 'file'], true)) {
            $output->writeln('<error>参数 --cache-type 非法，可选值：redis / redis_cluster / file。</error>');
            throw new RuntimeException('参数校验失败');
        }
        $output->writeln('');
        return [
            'redis_host' => (string) $redisHost,
            'redis_port' => (string) $redisPort,
            'redis_pass' => (string) $redisPass,
            'cache_type' => (string) $cacheType,
            'redis_prefix' => (string) $redisPrefix,
        ];
    }

    /**
     * 步骤 3：询问项目运行端口
     * @param mixed $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function askListenPort($helper, InputInterface $input, OutputInterface $output): string
    {
        $output->writeln('<comment>[3/3] 服务端口配置</comment>');
        $listenPort = $this->optionOrDefault($input, 'listen-port', null, true, true) ?? (string) $helper->ask($input, $output, new Question('请输入项目运行端口 [39000]: ', '39000'));
        if (!$this->validatePort($listenPort, $msg)) {
            $output->writeln("<error>参数 --listen-port 非法：{$msg}</error>");
            throw new RuntimeException('参数校验失败');
        }
        $output->writeln('');
        return (string) $listenPort;
    }

    /**
     * 打印安装完成提示
     * @param string $basePath    项目根目录
     * @param string $listenPort
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function printComplete(string $basePath, string $listenPort, OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln('<info>========== XbCode 安装配置完成！ ==========</info>');
        $output->writeln('<info>监听端口: ' . $listenPort . '</info>');
        // 只按当前运行系统提示对应的启动命令
        $output->writeln('<comment>运行环境: ' . PHP_OS_FAMILY . '</comment>');
        $output->writeln('<comment>启动命令: ' . $this->getStartCommand() . '</comment>');
        // Windows 下 windows.bat 与 php windows.php 作用重复，安装完成后清理
        $this->removeWindowsBat($basePath, $output);
    }

    /**
     * 删除项目根目录的 windows.bat（仅 Windows 环境执行，其余系统跳过）
     * @param string $basePath 项目根目录
     * @param OutputInterface $output
     * @param string|null $osFamily 运行系统族，为 null 时取当前系统，传入固定值便于验证
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function removeWindowsBat(string $basePath, OutputInterface $output, ?string $osFamily = null): void
    {
        $osFamily = $osFamily ?? PHP_OS_FAMILY;
        if ('Windows' !== $osFamily) {
            return;
        }
        $batFile = rtrim(str_replace('\\', '/', $basePath), '/') . '/windows.bat';
        if (!is_file($batFile)) {
            return;
        }
        if (@unlink($batFile)) {
            $output->writeln('  <info>已删除(Windows 冗余启动脚本): ' . $batFile . '</info>');
            return;
        }
        $output->writeln('  <comment>删除失败(Windows 冗余启动脚本)，可手动移除: ' . $batFile . '</comment>');
    }

    /**
     * 按运行系统返回启动命令，Windows 走 windows.php，Linux / Mac 走 start.php
     * @param string|null $osFamily 运行系统族，为 null 时取当前系统，传入固定值便于验证
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getStartCommand(?string $osFamily = null): string
    {
        $osFamily = $osFamily ?? PHP_OS_FAMILY;
        return 'Windows' === $osFamily ? 'php windows.php' : 'php start.php start';
    }

    /**
     * 获取项目根目录
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getBasePath(): string
    {
        // 优先用 webman 引导后可用的 base_path()，避免目录层级理解偏差
        if (function_exists('base_path')) {
            return rtrim(str_replace('\\', '/', base_path()), '/');
        }
        // 兜底：__DIR__ = plugin/xbCode/command，向上 3 级即项目根（xbcode）
        return rtrim(str_replace('\\', '/', dirname(__DIR__, 3)), '/');
    }

    /**
     * 获取所有插件目录
     *
     * @param string $basePath
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function getPluginDirs(string $basePath): array
    {
        $pluginDir = $basePath . '/plugin';
        if (!is_dir($pluginDir)) {
            return [];
        }
        $dirs = [];
        foreach (glob($pluginDir . '/*', GLOB_ONLYDIR) as $dir) {
            $dirs[] = $dir;
        }
        return $dirs;
    }

    /**
     * 写入 nginx 伪静态配置到根目录
     * @param string $basePath
     * @param string|int $port
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function writeNginxConf(string $basePath, $port, OutputInterface $output): void
    {
        $port = (string) $port;
        $tplFile = $basePath . '/plugin/xbCode/data/nginx.conf.tpl';
        if (!is_file($tplFile)) {
            throw new RuntimeException("nginx伪静态模板不存在：{$tplFile}");
        }
        $content = file_get_contents($tplFile);
        $content = strtr($content, ['{{port}}' => $port]);

        $target = $basePath . '/nginx.conf';
        file_put_contents($target, $content);
        $output->writeln('  <info>已写入: ' . $target . '</info>');
    }

    /**
     * 复制配置文件到主项目配置文件
     * @param string $basePath
     * @param string|int $port
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function writeConfigPhp(string $basePath, $port, OutputInterface $output): void
    {
        $dataConfigDir = $basePath . '/plugin/xbCode/data/config';
        $rootConfigDir = $basePath . '/config';
        // 用户要求: 统一以 plugin/xbCode/data/config/*.php 作为权威模板，直接复制覆盖到根 config/，
        // 这些模板内部已经用 Env::get() 动态读取 DB_* / REDIS_* / SESSION_* / CACHE_TYPE 等 .env 配置。
        $copyMap = [
            'process进程配置文件' => 'process.php',
            'database数据库配置文件' => 'database.php',
            'think-orm数据库配置文件' => 'think-orm.php',
            'Redis配置文件' => 'redis.php',
            'session配置文件' => 'session.php',
        ];
        foreach ($copyMap as $label => $filename) {
            $srcFile = $dataConfigDir . '/' . $filename;
            if (!is_file($srcFile)) {
                throw new RuntimeException("复制{$label}失败：权威模板 {$srcFile} 不存在");
            }
            $dstFile = $rootConfigDir . '/' . $filename;
            $copied = @copy($srcFile, $dstFile);
            if ($copied === false) {
                throw new RuntimeException("复制{$label}失败：{$srcFile} -> {$dstFile}");
            }
            $output->writeln('  <info>已覆盖(' . $label . '): ' . $dstFile . '</info>');
        }
        $output->writeln('');
    }

    /**
     * 写入根目录的 .env 文件
     * @param string $basePath
     * @param array $config
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function writeEnvFile(string $basePath, array $config, OutputInterface $output): void
    {
        $appDebug = 'false';

        $dbHost = $config['db_host'] ?? '127.0.0.1';
        $dbName = $config['db_name'] ?? 'xb_code';
        $dbUser = $config['db_user'] ?? 'root';
        $dbPass = $config['db_pass'] ?? '';
        $dbPort = $config['db_port'] ?? '3306';
        $dbPrefix = $config['db_prefix'] ?? 'xb_';

        $cacheType = $config['cache_type'] ?? 'redis';

        $redisHost = $config['redis_host'] ?? '127.0.0.1';
        $redisPort = $config['redis_port'] ?? '6379';
        $redisPass = $config['redis_pass'] ?? '';
        $redisPrefix = $config['redis_prefix'] ?? 'xb_cache_';

        $tplFile = $basePath . '/plugin/xbCode/data/env.tpl';
        if (!is_file($tplFile)) {
            throw new RuntimeException(".env模板不存在：{$tplFile}");
        }
        $content = file_get_contents($tplFile);
        $content = strtr($content, [
            '{{appDebug}}' => $appDebug,
            '{{dbHost}}' => $dbHost,
            '{{dbName}}' => $dbName,
            '{{dbUser}}' => $dbUser,
            '{{dbPass}}' => $dbPass,
            '{{dbPort}}' => $dbPort,
            '{{dbPrefix}}' => $dbPrefix,
            '{{cacheType}}' => $cacheType,
            '{{redisHost}}' => $redisHost,
            '{{redisPort}}' => $redisPort,
            '{{redisPass}}' => $redisPass,
            '{{redisPrefix}}' => $redisPrefix,
        ]);

        $target = $basePath . '/.env';
        file_put_contents($target, $content);
        $output->writeln('  <info>已写入: ' . $target . '</info>');
    }
}
