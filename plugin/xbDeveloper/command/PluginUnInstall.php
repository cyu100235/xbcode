<?php
namespace plugin\xbDeveloper\command;

use plugin\xbDeveloper\api\PluginsUnInstall;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 开发者插件卸载
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginUnInstall extends Command
{
    protected static $defaultName        = 'xb-plugin:uninstall';
    protected static $defaultDescription = 'Xb Plugin uninstall';
    
    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Xb plugin name');
    }

    /**
     * 执行命令
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        if (empty($name)) {
            $output->writeln('<error>请填写插件标识</error>');
            return self::FAILURE;
        }
        // 实例脚本
        $install = new PluginsUnInstall;
        // 执行脚本
        $install->start('script', $name, '1.0.0', 100);
        // 操作完成
        $install->start('complete', $name, '1.0.0', 100);
        // 输出信息
        $output->writeln("<info>插件[{$name}]卸载成功...</info>");
        // 输出成功码
        return self::SUCCESS;
    }
}
