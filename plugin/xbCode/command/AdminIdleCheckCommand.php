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

use plugin\xbCode\api\AdminSession;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 后台登录空闲检测
 *
 * 定期清理超过「登录空闲超时」仍无任何请求的后台登录会话，
 * 中间件在收到请求时会即时判定，本命令负责清理再也不会发起请求的会话。
 *
 * 命令示例：
 *   php webman admin:check:idle
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminIdleCheckCommand extends Command
{
    /**
     * 命令名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultName = 'admin:check:idle';

    /**
     * 命令描述
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultDescription = '检测后台登录空闲超时会话，超时未操作自动踢出登录';

    /**
     * 执行命令
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $count = AdminSession::sweepIdle();
        } catch (\Throwable $th) {
            $output->writeln("<fg=red>后台登录空闲检测失败：{$th->getMessage()}</fg=red>");
            return self::FAILURE;
        }
        $output->writeln("<fg=green>后台登录空闲检测完成，共踢出 {$count} 个会话...</fg=green>");
        return self::SUCCESS;
    }
}
