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

use plugin\xbCode\command\init\InitDel;
use plugin\xbCode\command\init\InitGit;
use plugin\xbCode\command\init\InitJwt;
use plugin\xbCode\command\init\InitCopy;
use plugin\xbCode\command\init\InitCheck;
use plugin\xbCode\command\init\InitDeploy;
use plugin\xbCode\command\init\InitReadme;
use plugin\xbCode\command\init\InitPreview;
use plugin\xbCode\command\init\InitComposers;
use Symfony\Component\Console\Command\Command;
use plugin\xbCode\command\init\InitMiddleware;
use plugin\xbCode\command\init\InitComposerFile;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 安装composer依赖
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginInit extends Command
{
    /**
     * 命令名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultName = 'xb-plugin:init';

    /**
     * 命令描述
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static $defaultDescription = 'Xb Plugin init Install';

    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure()
    {
        $this->addOption('middleware', null, InputOption::VALUE_NONE, '仅执行安装超全局中间件');
        $this->addOption('git', null, InputOption::VALUE_NONE, '仅执行安装插件目录下git文件');
        $this->addOption('jwt', null, InputOption::VALUE_NONE, '仅执行安装JWT密钥');
        $this->addOption('composer', null, InputOption::VALUE_NONE, '仅执行安装composer文件改造');
        $this->addOption('copy', null, InputOption::VALUE_NONE, '仅执行复制文件操作');
        $this->addOption('del', null, InputOption::VALUE_NONE, '仅执行删除文件操作');
        $this->addOption('composers', null, InputOption::VALUE_NONE, '仅执行安装所有依赖（composer install）');
        $this->addOption('deploy', null, InputOption::VALUE_NONE, '仅执行复制deploy文件');
        $this->addOption('readme', null, InputOption::VALUE_NONE, '仅执行复制README文件');
        $this->addOption('preview', null, InputOption::VALUE_NONE, '仅执行复制preview目录');
    }

    /**
     * 执行命令
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // 检查禁用函数
            InitCheck::checkDisabledFunction();
            // 检查扩展
            InitCheck::checkExtension();
        } catch (\Throwable $th) {
            $output->writeln("<fg=red>{$th->getMessage()}</fg=red>");
            return self::FAILURE;
        }
        // 检测是否传入了任意参数选项
        $hasOption = $input->getOption('middleware')
            || $input->getOption('git')
            || $input->getOption('jwt')
            || $input->getOption('composer')
            || $input->getOption('copy')
            || $input->getOption('del')
            || $input->getOption('composers')
            || $input->getOption('deploy')
            || $input->getOption('readme')
            || $input->getOption('preview');
        if (!$hasOption) {
            // 无参数时执行所有操作
            InitComposers::execute($input, $output);
            InitMiddleware::execute($input, $output);
            InitGit::execute($input, $output);
            InitJwt::execute($input, $output);
            InitComposerFile::execute($input, $output);
            InitCopy::execute($input, $output);
            InitDel::execute($input, $output);
        } else {
            if ($input->getOption('composers')) {
                // 安装所有依赖
                InitComposers::execute($input, $output);
            }
            if ($input->getOption('middleware')) {
                // 安装超全局中间件
                InitMiddleware::execute($input, $output);
            }
            if ($input->getOption('git')) {
                // 安装插件目录下git文件
                InitGit::execute($input, $output);
            }
            if ($input->getOption('jwt')) {
                // 安装JWT密钥
                InitJwt::execute($input, $output);
            }
            if ($input->getOption('composer')) {
                // 安装composer文件改造
                InitComposerFile::execute($input, $output);
            }
            if ($input->getOption('copy')) {
                // 执行复制文件
                InitCopy::execute($input, $output);
            }
            if ($input->getOption('deploy')) {
                InitDeploy::execute($input, $output);
            }
            if ($input->getOption('readme')) {
                InitReadme::execute($input, $output);
            }
            if ($input->getOption('preview')) {
                InitPreview::execute($input, $output);
            }
            if ($input->getOption('del')) {
                // 执行删除文件
                InitDel::execute($input, $output);
            }
        }
        // 返回完成
        return self::SUCCESS;
    }
}
