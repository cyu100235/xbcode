<?php
/**
 * 安装框架composer文件改造
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
 * 安装框架composer文件改造
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InitComposerFile
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
        $composerPath = base_path('composer.json');
        if (!file_exists($composerPath)) {
            $output->writeln("composer.json 文件不存在...");
            return;
        }
        $composer = json_decode(file_get_contents($composerPath), true);
        if (!isset($composer)) {
            $output->writeln("composer.json 文件解析失败...");
            return;
        }
        // 改造name
        $composer['name'] = 'xbcode/xbcode';
        // 改造key
        $composer['keywords'] = ['xbCode', 'xbCode-plugin', 'plugin'];
        // 改造homepage
        $composer['homepage'] = 'http://www.xbcode.net';
        // 改造license
        $composer['license'] = 'Apache-2.0';
        // 改造description
        $composer['description'] = '积木云核心框架，提供插件化开发基础能力';
        // 改造authors
        $composer['authors'] = [
            [
                'name' => '贵州积木云网络科技有限公司',
                'email' => '958416459@qq.com',
                'role' => 'Developer',
            ],
            [
                'name' => '楚羽幽',
                'email' => '958416459@qq.com',
                'role' => 'Developer',
            ],
        ];
        // 改造support
        $composer['support'] = [
            'email' => '958416459@qq.com',
            'qq' => '958416459',
            'wechat' => 'cyu107761',
            'issues' => 'https://gitee.com/xbcode_net/xbcode/issues',
            'source' => 'http://www.xbcode.net'
        ];
        // 改造安装冲突解决
        $composer['config']['audit']['block-insecure'] = false;
        file_put_contents($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $output->writeln("安装框架composer文件改造完成...");
    }
}
