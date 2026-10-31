<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\command;

use plugin\xbCode\api\Menus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 重建后台菜单
 *
 * 清空菜单表并重置自增ID，再以各插件 config/menu.php 为准重新导入。
 *
 * 命令示例：
 *   php webman xb-menu:recreate
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class MenuRecreateCommand extends Command
{
    /**
     * 命令名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultName = 'xb-menu:recreate';

    /**
     * 命令描述
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultDescription = '重建后台菜单（清空后按插件菜单文件重新导入）';

    /**
     * 执行命令
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            Menus::recreate();
        } catch (\Throwable $th) {
            $output->writeln("<fg=red>菜单重建失败：{$th->getMessage()}</fg=red>");
            return self::FAILURE;
        }

        $output->writeln('<info>菜单重建完成</info>');
        return self::SUCCESS;
    }
}
