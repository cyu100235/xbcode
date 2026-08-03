<?php
/**
 * 安装插件目录git文件
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\command\init;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 安装插件目录git文件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InitGit
{
    /**
     * 执行安装
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function execute(InputInterface $input, OutputInterface $output): void
    {
        $path = base_path('plugin/.gitignore');
        $content = <<<STR
        *
        !.gitignore
        STR;
        file_put_contents($path, $content);
        $output->writeln("安装插件目录下git文件完成...");
    }
}
