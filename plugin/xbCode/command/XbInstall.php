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

use plugin\xbCode\utils\FrameUtil;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use plugin\xbCode\command\install\InstallRunner;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use plugin\xbCode\command\install\EnvironmentUtil;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * 系统安装命令
 *
 * 命令示例：
 *
 * 1. 交互式安装（不传参数，逐步引导填写）
 *      php webman xb-install
 *
 * 2. 系统已安装时强制重新安装
 *      php webman xb-install --force
 *
 * 3. 一键安装 - 简化版（不带 Redis 参数）
 *      php webman xb-install --host=127.0.0.1 --port=3306 --database=xbcode --username=root --password=123456 --prefix=xb_ --web-name=积木云 --web-url=http://localhost --admin-user=admin --admin-pass=123456
 *
 * 4. 一键安装 - 完整版（包含 Redis 参数）
 *      php webman xb-install --host=127.0.0.1 --port=3306 --database=xbcode --username=root --password=123456 --prefix=xb_ --redis --redis-host=127.0.0.1 --redis-port=6379 --redis-password=redis_pass --redis-prefix=xb_ --web-name=积木云 --web-url=http://localhost --admin-user=admin --admin-pass=123456
 *
 * 参数齐全时不会进入交互，直接执行安装；缺失的参数仍会通过交互向导追问。
 * 已安装环境下重新安装请追加 --force 参数。
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class XbInstall extends Command
{
    /**
     * 命令名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultName = 'xb-install';

    /**
     * 命令描述
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultDescription = 'XbCode 系统安装（环境检测、填写安装信息、执行安装）';

    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure()
    {
        $this->addOption('force', null, InputOption::VALUE_NONE, '系统已安装时强制重新安装');
        // 数据库配置
        $this->addOption('host', null, InputOption::VALUE_REQUIRED, '数据库主机地址，默认 127.0.0.1');
        $this->addOption('port', null, InputOption::VALUE_REQUIRED, '数据库端口，默认 3306');
        $this->addOption('database', null, InputOption::VALUE_REQUIRED, '数据库名称');
        $this->addOption('username', null, InputOption::VALUE_REQUIRED, '数据库用户名，默认 root');
        $this->addOption('password', null, InputOption::VALUE_REQUIRED, '数据库密码');
        $this->addOption('prefix', null, InputOption::VALUE_REQUIRED, '数据表前缀，默认 xb_');
        // Redis配置
        $this->addOption('redis', null, InputOption::VALUE_NONE, '启用Redis缓存');
        $this->addOption('redis-host', null, InputOption::VALUE_REQUIRED, 'Redis主机地址，默认 127.0.0.1');
        $this->addOption('redis-port', null, InputOption::VALUE_REQUIRED, 'Redis端口，默认 6379');
        $this->addOption('redis-password', null, InputOption::VALUE_REQUIRED, 'Redis密码，可留空');
        $this->addOption('redis-prefix', null, InputOption::VALUE_REQUIRED, 'Redis前缀，默认 xb_');
        // 站点配置
        $this->addOption('web-name', null, InputOption::VALUE_REQUIRED, '站点名称，默认 积木云');
        $this->addOption('web-url', null, InputOption::VALUE_REQUIRED, '站点域名，默认 http://localhost');
        $this->addOption('admin-user', null, InputOption::VALUE_REQUIRED, '管理员账号，不少于5位，默认 admin');
        $this->addOption('admin-pass', null, InputOption::VALUE_REQUIRED, '管理员密码，6-20位，默认 123456');
    }

    /**
     * 执行命令
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('');
        $output->writeln('<info>================ 积木云系统安装 ================</info>');
        // 检测是否已经安装
        if (FrameUtil::checked() && !$input->getOption('force')) {
            $output->writeln('');
            $output->writeln('<error>系统已经安装完成，如需重新安装请使用 --force 参数</error>');
            return self::FAILURE;
        }
        try {
            // 1. 环境检测
            $output->writeln('');
            $output->writeln('<comment>[1/5] 环境检测</comment>');
            if (!$this->checkEnvironment($output)) {
                $output->writeln('');
                $output->writeln('<error>环境检测未通过，请处理后重试安装</error>');
                return self::FAILURE;
            }
            // 2. 填写安装信息
            $output->writeln('');
            $output->writeln('<comment>[2/5] 填写安装信息</comment>');
            $database = $this->askDatabase($input, $output);
            $redis = $this->askRedis($input, $output);
            $site = $this->askSite($input, $output);
            // 安装执行器
            $runner = new InstallRunner($database, $redis, $site, $output);
            // 安装前置预检
            $runner->precheck();
            $output->writeln('<info>安装信息校验通过</info>');
            // 3. 执行安装
            $output->writeln('');
            $output->writeln('<comment>[3/5] 执行安装</comment>');
            $runner->installStructure();
            $runner->installData();
            // 4. 写入安装配置
            $output->writeln('');
            $output->writeln('<comment>[4/5] 写入安装配置</comment>');
            $runner->writeConfig();
            // 5. 输出安装信息
            $output->writeln('');
            $output->writeln('<comment>[5/5] 输出安装信息</comment>');
            $info = $runner->getCompleteInfo();
            $this->showComplete($output, $info, $database, $redis, $site, $runner->getServerPort());
        } catch (\Throwable $th) {
            $output->writeln('');
            $output->writeln("<error>安装失败：{$th->getMessage()}</error>");
            return self::FAILURE;
        }
        return self::SUCCESS;
    }

    /**
     * 环境检测（输出检测结果）
     * @param OutputInterface $output
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function checkEnvironment(OutputInterface $output): bool
    {
        $data = EnvironmentUtil::get();
        $passed = true;
        foreach ($data as $group) {
            $rows = [];
            foreach ($group['children'] as $item) {
                $status = !empty($item['status']);
                if (!$status) {
                    $passed = false;
                }
                $rows[] = [
                    $item['title'],
                    $item['value'] ?? '',
                    $item['status_text'] ?? '',
                    $status ? '<info>通过</info>' : '<error>失败</error>',
                ];
            }
            $output->writeln("<info>{$group['title']}</info>");
            $table = new Table($output);
            $table->setHeaders(['检测项', '当前值', '说明', '结果']);
            $table->setRows($rows);
            $table->render();
        }
        return $passed;
    }

    /**
     * 填写并校验数据库配置
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Throwable
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function askDatabase(InputInterface $input, OutputInterface $output): array
    {
        $names = ['host', 'port', 'database', 'username', 'password', 'prefix'];
        // 一键安装：参数齐全时直接校验，不再交互
        if ($this->hasOptions($input, $names)) {
            $database = $this->makeDatabase($input, $output);
            InstallRunner::verifyDatabase($database);
            return $database;
        }
        while (true) {
            $output->writeln('<info>---- 数据库配置 ----</info>');
            $database = $this->makeDatabase($input, $output);
            try {
                InstallRunner::verifyDatabase($database);
                return $database;
            } catch (\Throwable $th) {
                $output->writeln("<error>数据库校验失败：{$th->getMessage()}</error>");
                if (!$input->isInteractive() || $this->hasAnyOption($input, $names)) {
                    throw $th;
                }
                $output->writeln('<comment>请重新填写数据库配置...</comment>');
            }
        }
    }

    /**
     * 构造数据库配置（命令行参数优先，其次交互填写）
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function makeDatabase(InputInterface $input, OutputInterface $output): array
    {
        return [
            'type' => 'mysql',
            'host' => $this->field($input, $output, 'host', '数据库主机地址', '127.0.0.1'),
            'port' => $this->field($input, $output, 'port', '数据库端口', '3306'),
            'database' => $this->field($input, $output, 'database', '数据库名称'),
            'username' => $this->field($input, $output, 'username', '数据库用户名', 'root'),
            'password' => $this->field($input, $output, 'password', '数据库密码'),
            'prefix' => $this->field($input, $output, 'prefix', '数据表前缀', 'xb_'),
        ];
    }

    /**
     * 填写并校验Redis配置
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Throwable
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function askRedis(InputInterface $input, OutputInterface $output): array
    {
        // 是否启用Redis：--redis 参数或提供了Redis连接参数即视为启用
        $enabled = $input->getOption('redis') || $this->hasOptions($input, ['redis-host', 'redis-port', 'redis-prefix']);
        if (!$enabled) {
            $enabled = $input->isInteractive() ? $this->confirm($input, $output, '是否启用Redis缓存 (y/N)', false) : false;
        }
        if (!$enabled) {
            return ['enabled' => '10'];
        }
        $names = ['redis-host', 'redis-port', 'redis-prefix'];
        // 一键安装：参数齐全时直接校验，不再交互
        if ($this->hasOptions($input, $names)) {
            $redis = $this->makeRedis($input, $output, true);
            InstallRunner::verifyRedis($redis);
            return $redis;
        }
        while (true) {
            $output->writeln('<info>---- Redis配置 ----</info>');
            $redis = $this->makeRedis($input, $output);
            try {
                InstallRunner::verifyRedis($redis);
                return $redis;
            } catch (\Throwable $th) {
                $output->writeln("<error>Redis校验失败：{$th->getMessage()}</error>");
                if (!$input->isInteractive() || $this->hasAnyOption($input, $names)) {
                    throw $th;
                }
                $output->writeln('<comment>请重新填写Redis配置...</comment>');
            }
        }
    }

    /**
     * 构造Redis配置（命令行参数优先，其次交互填写）
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param bool $fromOptions 一键安装模式：密码等可选项直接取参数，不进入交互
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function makeRedis(InputInterface $input, OutputInterface $output, bool $fromOptions = false): array
    {
        return [
            'enabled' => '20',
            'host' => $this->field($input, $output, 'redis-host', 'Redis主机地址', '127.0.0.1'),
            'port' => $this->field($input, $output, 'redis-port', 'Redis端口', '6379'),
            'password' => $fromOptions
                ? (string) ($input->getOption('redis-password') ?? '')
                : $this->field($input, $output, 'redis-password', 'Redis密码（可留空）'),
            'prefix' => $this->field($input, $output, 'redis-prefix', 'Redis前缀', 'xb_'),
        ];
    }

    /**
     * 填写并校验站点配置
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Throwable
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function askSite(InputInterface $input, OutputInterface $output): array
    {
        $names = ['web-name', 'web-url', 'admin-user', 'admin-pass'];
        // 一键安装：参数齐全时直接校验，不再交互
        if ($this->hasOptions($input, $names)) {
            $site = $this->makeSite($input, $output);
            InstallRunner::verifySite($site);
            return $site;
        }
        while (true) {
            $output->writeln('<info>---- 站点配置 ----</info>');
            $site = $this->makeSite($input, $output);
            try {
                InstallRunner::verifySite($site);
                return $site;
            } catch (\Throwable $th) {
                $output->writeln("<error>站点校验失败：{$th->getMessage()}</error>");
                if (!$input->isInteractive() || $this->hasAnyOption($input, $names)) {
                    throw $th;
                }
                $output->writeln('<comment>请重新填写站点配置...</comment>');
            }
        }
    }

    /**
     * 构造站点配置（命令行参数优先，其次交互填写）
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function makeSite(InputInterface $input, OutputInterface $output): array
    {
        return [
            'web_name' => $this->field($input, $output, 'web-name', '站点名称', '积木云'),
            'web_url' => $this->field($input, $output, 'web-url', '站点域名', 'http://localhost'),
            'username' => $this->field($input, $output, 'admin-user', '管理员账号（不少于5位）', 'admin'),
            'password' => $this->field($input, $output, 'admin-pass', '管理员密码（6-20位）', '123456'),
        ];
    }

    /**
     * 输出安装完成信息（含全部安装参数）
     * @param OutputInterface $output
     * @param array $info 完成信息（提示消息、后台地址）
     * @param array $database 数据库配置
     * @param array $redis Redis配置
     * @param array $site 站点配置
     * @param int $serverPort 内网访问端口
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function showComplete(OutputInterface $output, array $info, array $database, array $redis, array $site, int $serverPort = 0): void
    {
        // 站点访问地址：站点域名 + 内网访问端口
        $domain = rtrim((string) ($site['web_url'] ?? ''), '/');
        $domainPort = $serverPort > 0 ? "{$domain}:{$serverPort}" : $domain;
        // 后台访问地址：访问地址 + 后台路径（去掉末尾斜杠）
        $adminUrl = rtrim($domainPort . ($info['admin_url'] ?? ''), '/');
        // 是否启用Redis
        $redisEnabled = ($redis['enabled'] ?? '10') === '20';

        $output->writeln('');
        $output->writeln("<info>温馨提示：{$info['message']}</info>");

        $output->writeln('');
        $output->writeln('<info>数据库信息</info>');
        $output->writeln('主机地址：' . ($database['host'] ?? ''));
        $output->writeln('数据库端口：' . ($database['port'] ?? ''));
        $output->writeln('数据库名称：' . ($database['database'] ?? ''));
        $output->writeln('数据库用户名：' . ($database['username'] ?? ''));
        $output->writeln('数据库密码：' . ($database['password'] ?? ''));
        $output->writeln('数据表前缀：' . ($database['prefix'] ?? ''));

        $output->writeln('');
        $output->writeln('<info>Redis信息</info>');
        $output->writeln('是否启用Redis：' . ($redisEnabled ? '是' : '否'));
        $output->writeln('Redis主机地址：' . ($redis['host'] ?? '127.0.0.1'));
        $output->writeln('Redis端口：' . ($redisEnabled ? ($redis['port'] ?? '') : '-'));
        $output->writeln('Redis密码：' . ($redisEnabled ? ($redis['password'] ?? '') : '-'));
        $output->writeln('Redis前缀：' . ($redisEnabled ? ($redis['prefix'] ?? '') : '-'));

        $output->writeln('');
        $output->writeln('<info>根据环境输出启动命令</info>');
        $output->writeln('Linux启动命令：php webman start');
        $output->writeln('Windows启动命令：php windows.php');

        $output->writeln('');
        $output->writeln('站点名称：' . ($site['web_name'] ?? ''));
        $output->writeln('首页访问：' . $domainPort);
        $output->writeln('后台访问：' . $adminUrl);
        $output->writeln('登录账号：' . ($site['username'] ?? ''));
        $output->writeln('登录密码：' . ($site['password'] ?? ''));
    }

    /**
     * 交互式提问
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $label 提示文本
     * @param string $default 默认值
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function ask(InputInterface $input, OutputInterface $output, string $label, string $default = ''): string
    {
        $prompt = $default === '' ? "{$label}: " : "{$label} [{$default}]: ";
        $question = new Question($prompt, $default);
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        return trim((string) $helper->ask($input, $output, $question));
    }

    /**
     * 获取字段值（命令行参数优先，其次交互填写）
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $option 命令行参数名
     * @param string $label 提示文本
     * @param string $default 默认值
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function field(InputInterface $input, OutputInterface $output, string $option, string $label, string $default = ''): string
    {
        // 命令行参数优先
        $value = $input->getOption($option);
        if ($value !== null) {
            return trim((string) $value);
        }
        // 交互式填写
        return $this->ask($input, $output, $label, $default);
    }

    /**
     * 检测一组命令行参数是否全部提供
     * @param InputInterface $input
     * @param array $names 参数名列表
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function hasOptions(InputInterface $input, array $names): bool
    {
        foreach ($names as $name) {
            if ($input->getOption($name) === null) {
                return false;
            }
        }
        return true;
    }

    /**
     * 检测一组命令行参数是否存在任意一个
     * @param InputInterface $input
     * @param array $names 参数名列表
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function hasAnyOption(InputInterface $input, array $names): bool
    {
        foreach ($names as $name) {
            if ($input->getOption($name) !== null) {
                return true;
            }
        }
        return false;
    }

    /**
     * 交互式确认
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $label 提示文本
     * @param bool $default 默认值
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function confirm(InputInterface $input, OutputInterface $output, string $label, bool $default = false): bool
    {
        $question = new ConfirmationQuestion("{$label}: ", $default);
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        return (bool) $helper->ask($input, $output, $question);
    }
}
