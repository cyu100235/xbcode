<?php
namespace plugin\xbCode\command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 安装composer依赖
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginComposer extends Command
{
    protected static $defaultName        = 'xb-plugin:composer';
    protected static $defaultDescription = 'Xb Plugin composer';
    
    /**
     * 配置命令
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
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
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 检测webman版本
        $this->checkWebmanVersion();
        // 获取所有composer
        $composer = $this->getComposerPackages();
        // 开始安装composer
        $this->installComposer($input, $output,$composer);
        // 返回完成
        return self::SUCCESS;
    }

    /**
     * 开始安装composer
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function installComposer(InputInterface $input, OutputInterface $output,array $data)
    {
        $date = date('Y-m-d');
        $dateTime = date('Y-m-d H:i:s');
        $composerLogPath = base_path() . "/runtime/logs/composer/{$date}/{$dateTime}.log";
        if (!is_dir(dirname($composerLogPath))) {
            mkdir(dirname($composerLogPath), 0777, true);
        }
        foreach ($data as $package) {
            $result = static::installComposerPackage($package);
            // 记录安装日志
            if ($result['state']) {
                $output->writeln("<fg=green>{$package} 安装成功</fg=green>");
            }else{
                $message = "【{$dateTime}】 {$result['command']}\n{$result['output']}\n";
                file_put_contents($composerLogPath, $message, FILE_APPEND);
                $output->writeln("<fg=red>{$package} 安装失败，请查看安装日志</fg=red>");
            }
        }
        $output->writeln("<fg=green>全部插件依赖安装完成，请重启框架...</fg=green>");
    }

    /**
     * 执行composer包安装
     * @param string $package
     * @throws \Exception
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function installComposerPackage(string $package) {
        // 确保composer命令存在
        $composerPath = trim(shell_exec('which composer'));
        if (empty($composerPath)) {
            throw new Exception('请先安装 Composer');
        }
        // 确保exec命令存在
        if (!function_exists('exec')) {
            throw new Exception('请先解除 exec 函数禁用');
        }
        // 构建composer require命令
        $command = "{$composerPath} require {$package} -n 2>&1";
        
        // 执行命令
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);
        
        // 返回执行结果
        return [
            'state' => $returnVar === 0,
            'output' => implode("\n", $output),
            'command' => $command
        ];
    }

    /**
     * 获取所有插件composer
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getComposerPackages() {
        $plugins = glob(base_path() . '/plugin/*/plugins.json');
        $data = [];
        foreach ($plugins as $path) {
            $plugin = file_get_contents($path);
            if (empty($plugin)) {
                continue;
            }
            $plugin = json_decode($plugin, true);
            if (empty($plugin['composer'])) {
                continue;
            }
            $data = array_merge($data, $plugin['composer']);
        }        
        return $data;
    }

    /**
     * 检查webman版本
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function checkWebmanVersion()
    {
        $installedFile = base_path() . '/vendor/composer/installed.php';
        if (!file_exists($installedFile)) {
            throw new Exception('未安装webman');
        }
        $versionInfo = include $installedFile;
        if (empty($versionInfo)) {
            throw new Exception('获取webman失败');
        }
        $webmanVersion = $versionInfo['versions']['workerman/webman-framework']['pretty_version'] ?? '';
        if (empty($webmanVersion)) {
            throw new Exception('获取webman版本号失败');
        }
        $version = 'v2.1.0';
        if (version_compare($webmanVersion, $version, '<')) {
            throw new Exception("Webman版本过低，请升级到{$version}以上版本");
        }
    }
}
