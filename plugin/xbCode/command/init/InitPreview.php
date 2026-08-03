<?php
/**
 * 复制preview目录
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
 * 复制preview目录
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InitPreview
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
        $sourceDir = base_path('plugin/xbCode/public/preview');
        $targetDir = base_path('public/preview');
        
        if (!is_dir($sourceDir)) {
            $output->writeln('<fg=red>预览图目录不存在...</fg=red>');
            return;
        }
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        $files = scandir($sourceDir);
        $count = 0;
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $sourceFile = $sourceDir . '/' . $file;
            $targetFile = $targetDir . '/' . $file;
            if (is_file($sourceFile)) {
                copy($sourceFile, $targetFile);
                $count++;
            }
        }
        
        $output->writeln("<fg=green>复制预览图目录完成，共复制 {$count} 个文件...</fg=green>");
    }
}
