<?php
/**
 * 删除多余配置文件
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
 * 删除多余配置文件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InitDel
{
    /**
     * 删除文件列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static array $delCopys = [
        [
            'path' => '/config/plugin/webman/channel/process.php',
            'remarks' => '删除channel初始配置文件完成...'
        ],
        [
            'path' => '/config/plugin/webman/redis-queue/process.php',
            'remarks' => '删除queue初始配置文件完成...'
        ],
    ];

    /**
     * 执行删除
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function execute(InputInterface $input, OutputInterface $output): void
    {
        foreach (self::$delCopys as $value) {
            $delPath = base_path($value['path']);
            if (!file_exists($delPath)) {
                $output->writeln($value['remarks']);
            }
            if (file_exists($delPath)) {
                unlink($delPath);
                $output->writeln($value['remarks']);
            }
            sleep(1);
        }
    }
}
