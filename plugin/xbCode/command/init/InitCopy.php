<?php
/**
 * 复制配置文件
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
 * 复制配置文件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InitCopy
{
    /**
     * 复制文件列表
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static array $copyFiles = [
        [
            'from' => '/plugin/xbCode/data/config/app.php',
            'to' => '/config/app.php',
            'remarks' => '安装app配置文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/nginx.conf',
            'to' => '/nginx.conf',
            'remarks' => '复制NGINX规则文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/README.md',
            'to' => '/README.md',
            'remarks' => '复制README文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/config/database.php',
            'to' => '/config/database.php',
            'remarks' => '替换Database配置文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/config/think-orm.php',
            'to' => '/config/think-orm.php',
            'remarks' => '替换TP-ORM配置文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/config/think-cache.php',
            'to' => '/config/think-cache.php',
            'remarks' => '替换TP-Cache配置文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/config/redis.php',
            'to' => '/config/redis.php',
            'remarks' => '替换Redis配置文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/data/queue/redis.php',
            'to' => '/config/plugin/webman/redis-queue/redis.php',
            'remarks' => '替换Redis队列配置文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/data/config/server.php',
            'to' => '/config/server.php',
            'remarks' => '替换server配置文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/data/config/process.php',
            'to' => '/config/process.php',
            'remarks' => '替换process配置文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/data/deploy.php',
            'to' => '/deploy.php',
            'remarks' => '替换deploy发布脚本文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/data/gitignore.tpl',
            'to' => '/.gitignore',
            'remarks' => '替换.gitignore文件完成...'
        ],
        [
            'from' => '/plugin/xbCode/LICENSE',
            'to' => '/LICENSE',
            'remarks' => '替换开源协议文件完成...'
        ],
    ];

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
        foreach (self::$copyFiles as $value) {
            self::replaceFile(
                base_path($value['from']),
                base_path($value['to']),
                $value['remarks'],
                $output
            );
            sleep(1);
        }
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
