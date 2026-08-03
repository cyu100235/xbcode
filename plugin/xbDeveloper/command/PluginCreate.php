<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\command;

use plugin\xbDeveloper\api\PluginsCreate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * 创建插件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginCreate extends Command
{
    protected static $defaultName = 'xb-plugin:create';
    protected static $defaultDescription = 'Xb Plugin Create';

    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function configure()
    {
        $this->addOption('title', 'title', InputOption::VALUE_OPTIONAL, 'Xb plugin title');
        $this->addOption('name', 'name', InputOption::VALUE_OPTIONAL, 'Xb plugin name');
        $this->addOption('author', 'author', InputOption::VALUE_OPTIONAL, 'Xb plugin author');
        $this->addOption('desc', 'desc', InputOption::VALUE_OPTIONAL, 'Xb plugin description');
        $this->addOption('gradient', 'gradient', InputOption::VALUE_OPTIONAL, 'Use gradient background (y/n)', 'n');
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
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // 检查是否通过命令行参数传入
        $title = $input->getOption('title');
        $name = $input->getOption('name');
        $author = $input->getOption('author');
        $desc = $input->getOption('desc');
        $gradientOption = $input->getOption('gradient');
        $gradientOption = strtolower($gradientOption);
        $quick = true;

        // 如果没有通过命令行参数传入，则使用交互式询问
        if (empty($title)) {
            $question = new Question('插件标题 (title)：');
            $title = $helper->ask($input, $output, $question);
            $quick = false;
        }

        if (empty($name)) {
            $question = new Question('插件标识 (name)：');
            $name = $helper->ask($input, $output, $question);
            $quick = false;
        }

        if (empty($author)) {
            $question = new Question('开发者名称 (author)：');
            $author = $helper->ask($input, $output, $question);
            $quick = false;
        }

        if (empty($desc)) {
            $question = new Question('一句话描述 (3-30字)：');
            $desc = $helper->ask($input, $output, $question);
            $quick = false;
        }

        // 验证必填字段
        if (empty($title)) {
            $output->writeln("<error>插件标题不能为空</error>");
            return self::FAILURE;
        }
        if (empty($name)) {
            $output->writeln("<error>插件标识不能为空</error>");
            return self::FAILURE;
        }
        if (empty($author)) {
            $output->writeln("<error>开发者名称不能为空</error>");
            return self::FAILURE;
        }
        if (empty($desc)) {
            $output->writeln("<error>一句话描述不能为空</error>");
            return self::FAILURE;
        }

        // 检测标识必须字母+数字
        if (!preg_match('/^[a-zA-Z0-9]+$/', $name)) {
            throw new \Exception('插件标识只能是字母+数字，不能包含特殊字符');
        }

        // 处理渐变色选项
        if ($gradientOption === 'y' || $gradientOption === 'yes') {
            $gradient = true;
        } elseif ($gradientOption === 'n' || $gradientOption === 'no') {
            $gradient = false;
        } else {
            // 如果没有有效的命令行参数，则询问用户
            $question = new ConfirmationQuestion('渐变色预览图？(y/n)：');
            $gradient = $helper->ask($input, $output, $question);
        }
        $gradientText = $gradient ? '是' : '否';


        try {
            // 创建参数
            $data = [
                'title' => $title,
                'name' => $name,
                'author' => $author,
                'desc' => $desc,
                'gradient' => $gradient,
            ];
            // 数据验证
            PluginsCreate::validate($data);

            // 是否快速创建
            if (!$quick) {
                // 输出插件创建信息
                echo "\n";
                $output->writeln("<info>----------插件信息----------</info>");
                $output->writeln("<info>插件标题：{$title}</info>");
                $output->writeln("<info>插件标识：{$name}</info>");
                $output->writeln("<info>开发者名称：{$author}</info>");
                $output->writeln("<info>一句话描述：{$desc}</info>");
                $output->writeln("<info>是否使用渐变色背景：{$gradientText}</info>");
                $output->writeln("<info>----------插件信息----------</info>");
                echo "\n";
                // 创建确认提示
                $question = new ConfirmationQuestion('认真确认信息后，是否创建插件？(y/n)', false);
                if (!$helper->ask($input, $output, $question)) {
                    return self::SUCCESS;
                }
                echo "\n";
            }
            // 开始创建插件
            $output->writeln("正在创建 {$name} 插件...");
            PluginsCreate::create($data, true);
        } catch (\Throwable $th) {
            $output->writeln("<error>{$th->getMessage()}</error>");
            return self::FAILURE;
        }
        $output->writeln("<info>{$name} 插件创建成功...</info>");
        return self::SUCCESS;
    }
}
