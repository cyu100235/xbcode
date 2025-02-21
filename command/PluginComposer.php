<?php
namespace plugin\xbCode\command;

use plugin\xbCode\api\Composer;
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
        // 安装所有插件依赖
        Composer::install();
        // 安装完成
        $output->writeln("<fg=green>全部插件依赖安装完成，请重启框架...</fg=green>");
        // 返回完成
        return self::SUCCESS;
    }
}
