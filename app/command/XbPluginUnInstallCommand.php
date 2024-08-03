<?php

namespace app\command;

use app\common\service\CloudSerivce;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use app\model\Plugins;

/**
 * 卸载小白插件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbPluginUnInstallCommand extends Command
{
    protected static $defaultName = 'xb-plugin:uninstall';
    protected static $defaultDescription = 'Xb Plugin uninstall';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Xb plugin name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $output->writeln("UnInstall Xb Plugin $name...");

        if (strpos($name, '/') !== false) {
            $output->writeln('<error>Bad name, name must not contain character \'/\'</error>');
            return self::FAILURE;
        }
        if (!class_exists("plugin\\$name\\Install")) {
            $output->writeln("<error>UnInstall class plugin\\$name\\Install not exists</error>");
            return self::FAILURE;
        }
        // 获取插件本地版本
        $version = CloudSerivce::getLocalPluginVersion($name);
        // 检测插件是否存在
        $plugin = Plugins::where(['name' => $name])->find();
        if (!$plugin) {
            $output->writeln("<error>{$name} Plugin does not exist or has not been imported</error>");
            return self::FAILURE;
        }
        if ($plugin['state'] == '10') {
            $output->writeln("<error>{$name} Plugin has been uninstalled</error>");
            return self::FAILURE;
        }
        // 执行数据安装
        self::installData($name, $version, 'uninstall');
        // 安装完成
        $output->writeln("UnInstall plugin {$name} success");
        // 返回结果
        return self::SUCCESS;
    }

    /**
     * 安装插件数据
     * @param string $name
     * @param string $version
     * @param string $methodName
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function installData(string $name, string $version, string $methodName)
    {
        try {
            // 插件类命名空间
            $class = "\\plugin\\{$name}\\Install";
            // 检测类是否存在
            if (!class_exists($class)) {
                throw new \Exception("插件卸载失败：{$class}");
            }
            // 上下文
            $context = null;
            // 前置
            if (method_exists($class, "{$methodName}Before")) {
                $context = call_user_func([new $class, "{$methodName}Before"]);
            }
            // 插件
            if (method_exists($class, $methodName)) {
                $context = call_user_func([new $class, $methodName], $context);
            }
            // 后置
            if (method_exists($class, "{$methodName}After")) {
                call_user_func([new $class, "{$methodName}After"], $context);
            }
            // 设置插件已卸载
            Plugins::where(['name' => $name])->save(['state' => '10']);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
