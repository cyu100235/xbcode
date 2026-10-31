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
use plugin\xbDeveloper\api\PluginsUpdate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 开发者插件更新
 * 复用 xbCode 更新流程（PluginsUpdateApi）
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginUpdate extends Command
{
    protected static $defaultName        = 'xb-plugin:update';
    protected static $defaultDescription = 'Xb Plugin update';

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
            if (!PluginsApi::make()->installed($name)) {
                throw new Exception("插件 {$name} 未安装，请先执行：php webman xb-plugin:install {$name}");
            }
            // 按步骤更新插件
            $update = new PluginsUpdate;
            foreach ($update->steps() as $step) {
                $result = $update->start($name, $plugin['version'], $step['name']);
                // 刷新插件缓存
                PluginsApi::make()->getCache(true);
                $output->writeln("<info>{$step['title']}：{$result['text']}</info>");
            }
        } catch (\Throwable $th) {
            $output->writeln("<error>{$th->getMessage()}</error>");
            return self::FAILURE;
        }
        // 输出信息
        $output->writeln("<info>插件[{$name}]更新成功...</info>");
        // 输出成功码
        return self::SUCCESS;
    }
}
