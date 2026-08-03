<?php
namespace plugin\xbDeveloper\command;

use Exception;
use plugin\xbDeveloper\api\PluginsExport;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 导出插件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginExport extends Command
{
    protected static $defaultName        = 'xb-plugin:export';
    protected static $defaultDescription = 'Xb Plugin export';
    
    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
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
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $method = $input->getArgument('method');
        if (empty($name)) {
            $output->writeln('<error>请填写插件标识</error>');
            return self::FAILURE;
        }
        $class = new PluginsExport;
        $methodName = 'exportAllData';
        if ($method) {
            // 首字母转大写
            $method = ucfirst($method);
            $methodName = "export{$method}";
            if (!method_exists($class, $methodName)) {
                $output->writeln('<error>方法不存在</error>');
                return self::FAILURE;
            }
        }
        // 执行导出方法
        call_user_func([$class, $methodName], $name);
        $messageTypes = [
            'Sql' => '表结构',
            'Dict' => '字典',
            'Menus' => '菜单',
            'Crontab' => '配置',
        ];
        $message = $messageTypes[$method] ?? '全部';
        // 提示导出成功
        $output->writeln("<info>插件 {$name} {$message}数据导出完成...</info>");
        // 导出成功
        return self::SUCCESS;
    }
}
