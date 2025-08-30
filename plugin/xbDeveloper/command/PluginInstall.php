<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\command;

use plugin\xbDeveloper\api\PluginsInstall;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * 开发者插件安装
 * 1.检测插件是否为开发者插件
 * 2.执行安装脚本
 * 3.执行安装完成
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginInstall extends Command
{
    protected static $defaultName = 'xb-plugin:install';
    protected static $defaultDescription = 'Xb Plugin install';

    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Xb plugin name');
        $this->addArgument('method', InputArgument::OPTIONAL, 'Xb plugin method');
    }

    /**
     * 执行命令
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $method = $input->getArgument('method');
        $method = $method ?: '';
        if (empty($name)) {
            $output->writeln('<error>请填写插件标识</error>');
            return self::FAILURE;
        }
        // 实例脚本
        $install = new PluginsInstall;
        // 执行脚本
        $script = $method ? 'installMethod' : 'script';
        $install->start($script, $name, '1.0.0', 100, $method);
        // 询问是否执行完成方法
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('是否执行完成方法，默认:n (y/n)', false);
        if ($helper->ask($input, $output, $question)) {
            $install->start('complete', $name, '1.0.0', 100);
        }
        // 输出信息
        $output->writeln("<info>插件[{$name}]安装成功...</info>");
        // 输出成功码
        return self::SUCCESS;
    }
}
