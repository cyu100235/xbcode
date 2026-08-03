<?php
/**
 * 安装全局中间件配置
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
 * 安装全局中间件配置
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InitMiddleware
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
        $middleware = <<<PHP
<?php

try {
    return [
        '@' => [
            \\plugin\\xbCode\\app\\middleware\\XbMiddleware::class,
            \\plugin\\xbCode\\app\\middleware\\PluginMiddleware::class,
        ],
    ];
} catch (\\Throwable \$th) {
    return [];
}
PHP;
        $middlewarePath = base_path('/config/middleware.php');
        file_put_contents($middlewarePath, $middleware);
        $output->writeln("安装全局中间件配置文件完成...");
    }
}
