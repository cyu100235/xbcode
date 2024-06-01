<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webman\Event\Event;

class XbEventCommand extends Command
{
    protected static $defaultName = 'xb-events:show';
    protected static $defaultDescription = 'show Xb events';

    /**
     * @return void
     */
    protected function configure()
    {
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->showEvents();
        return self::SUCCESS;
    }

    /**
     * 查看已注册的事件
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function showEvents()
    {
        $data = Event::list();
        $data = array_map(function ($item) {
            return [
                'name' => $item[0] ?? '',
                'class' => get_class($item[1][0]) ?? '',
                'method' => $item[1][1] ?? '',
            ];
        }, $data);
        $data = array_values($data);
        foreach ($data as $value) {
            echo "--------------------------------\n";
            echo "事件名称：{$value['name']}\n";
            echo "事件类名：{$value['class']}\n";
            echo "事件方法：{$value['method']}\n";
            echo "--------------------------------\n";
        }
    }
}
