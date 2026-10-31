<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\command;

use Exception;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\api\PluginsInstallApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 开发者插件安装
 * 复用 xbCode 后台安装流程（PluginsInstallApi）
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginInstall extends Command
{
    protected static $defaultName = 'xb-plugin:install';
    protected static $defaultDescription = 'Xb Plugin install';

    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Xb plugin name');
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
        $name = (string) $input->getArgument('name');
        try {
            // 获取插件信息
            $plugin = PluginsApi::make()->get($name);
            if (empty($plugin)) {
                throw new Exception("插件 {$name} 不存在");
            }
            // 检测插件是否已安装
            if (PluginsApi::make()->installed($name)) {
                throw new Exception("插件 {$name} 已安装，如需升级请执行：php webman xb-plugin:update {$name}");
            }
            // 按步骤安装插件
            $install = new PluginsInstallApi;
            foreach ($install->steps() as $step) {
                $result = $install->start($name, $plugin['version'], $step['name']);
                // 刷新插件缓存
                PluginsApi::make()->getCache(true);
                $output->writeln("<info>{$step['title']}：{$result['text']}</info>");
            }
        } catch (\Throwable $th) {
            $output->writeln("<error>{$th->getMessage()}</error>");
            return self::FAILURE;
        }
        // 输出信息
        $output->writeln("<info>插件[{$name}]安装成功...</info>");
        // 输出成功码
        return self::SUCCESS;
    }
}
