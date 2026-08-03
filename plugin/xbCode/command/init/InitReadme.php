<?php
/**
 * 复制README文件
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\command\init;

use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 复制README文件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InitReadme
{
    /**
     * 执行复制
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function execute(InputInterface $input, OutputInterface $output): void
    {
        self::replaceFile(
            base_path('plugin/xbCode/README.md'),
            base_path('README.md'),
            '复制README文件完成...',
            $output
        );
    }

    /**
     * 替换文件
     * @param string $oldPath
     * @param string $newPath
     * @param string $remarks
     * @param OutputInterface $output
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function replaceFile(string $oldPath, string $newPath, string $remarks, OutputInterface $output): void
    {
        if (!file_exists($oldPath)) {
            throw new Exception("{$oldPath} 文件不存在");
        }
        if (!is_dir(dirname($newPath))) {
            mkdir(dirname($newPath), 0755, true);
        }
        copy($oldPath, $newPath);
        $output->writeln($remarks);
    }
}
