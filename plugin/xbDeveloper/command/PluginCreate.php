<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\command;

use plugin\xbDeveloper\api\PluginsCreate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * 创建插件
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginCreate extends Command
{
    protected static $defaultName = 'xb-plugin:create';
    protected static $defaultDescription = 'Xb Plugin Create';

    /**
     * 配置命令
     * @return void
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function configure()
    {
        $this->addArgument('title', InputArgument::OPTIONAL, 'Xb plugin title');
        $this->addArgument('name', InputArgument::OPTIONAL, 'Xb plugin name');
        $this->addArgument('author', InputArgument::OPTIONAL, 'Xb plugin author');
        $this->addArgument('desc', InputArgument::OPTIONAL, 'Xb plugin description');
    }

    /**
     * 执行命令
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        // 插件标题
        $question = new Question('插件标题 (title)：');
        $title = $helper->ask($input, $output, $question);
        // 插件标识
        $question = new Question('插件标识 (name)：');
        $name = $helper->ask($input, $output, $question);
        // 插件标题
        $question = new Question('作者名称 (author)：');
        $author = $helper->ask($input, $output, $question);
        // 插件标题
        $question = new Question('一句话描述 (3-30字)：');
        $desc = $helper->ask($input, $output, $question);
        try {
            // 标识首字母转大写
            $name = ucfirst($name);
            $name = "xb{$name}";

            // 创建参数
            $data = [
                'title'=> $title,
                'name'=> $name,
                'author'=> $author,
                'desc' => $desc
            ];
            // 数据验证
            PluginsCreate::validate($data);
            
            // 输出插件创建信息
            echo "\n";
            $output->writeln("<info>----------插件信息----------</info>");
            $output->writeln("<info>插件标题：{$title}</info>");
            $output->writeln("<info>插件标识：{$name}</info>");
            $output->writeln("<info>作者名称：{$author}</info>");
            $output->writeln("<info>一句话描述：{$desc}</info>");
            $output->writeln("<info>----------插件信息----------</info>");
            // 创建确认提示
            $question = new ConfirmationQuestion('是否创建插件？默认:n (y/n)', false);
            if (!$helper->ask($input, $output, $question)) {
                return self::SUCCESS;
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
