<?php
/**
 * 安装Composer依赖
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\command\init;

use plugin\xbCode\api\Composer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 安装Composer依赖
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InitComposers
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
        $output->writeln("<fg=green>准备开始安装Composer依赖...</fg=green>");
        Composer::install();
        $output->writeln("<fg=green>全部Composer依赖安装完成...</fg=green>");
    }
}
