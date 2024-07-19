<?php
namespace app\command;

use hg\apidoc\parses\ParseAnnotation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 事件文档命令
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbPluginEventDocCommand extends Command
{
    // 命令名称
    protected static $defaultName = 'xb-plugin:event-doc';

    // 命令描述
    protected static $defaultDescription = 'Xb Plugin event doc';

    /**
     * 配置
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Xb plugin name');
    }

    /**
     * 执行
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $output->writeln("<info>Generating xb plugin document in progress...</info>");
        if (strpos($name, '/') !== false) {
            $output->writeln('<error>Bad name, name must not contain character \'/\'</error>');
            return self::FAILURE;
        }
        if (!class_exists("plugin\\$name\\Install")) {
            $output->writeln("<error>Install class plugin\\$name\\Install not exists</error>");
            return self::FAILURE;
        }
        // 生成文档
        $this->generate($input, $output);
        // 返回结果
        return self::SUCCESS;
    }

    /**
     * 生成事件文档
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function generate(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $data = $this->getEvents($name);
        // 生成文档
        $content = "# 事件文档\n\n";
        foreach ($data as $class) {
            // 生成类标题
            $content .= "## {$class['title']}\n\n";
            // 类描述
            if ($class['desc']) {
                $content .= "{$class['desc']}\n\n";
            }
            if (empty($class['methods'])) {
                continue;
            }
            foreach ($class['methods'] as $method) {
                // 方法名称
                $content .= "### {$method['title']}\n\n";
                $content .= "#### 方法名称 {$method['name']}\n\n";
                // 调用方式
                $content .= "#### 调用方式\n\n``` php \n\n// 事件名称 shop.event.GoodsEvent.add\n\n// 调用代码（同步方式）\nEvent::dispatch('shop.event.GoodsEvent.add', \$data);\n\n// 调用代码（异步方式）\nEvent::emit('shop.event.GoodsEvent.add', \$data);\n\n\n```\n\n";
                // 方法描述
                if ($method['desc']) {
                    $content .= "{$method['desc']}\n\n";
                }
                // 拼接参数
                $content .= "#### 方法参数\n\n";
                $content .= "| 参数名 | 必填 | 说明 | 类型 | 默认值  |";
                $content .= "\n";
                $content .= "| ----- | ----- | ----- | ----- | ----- |";
                $content .= "\n";
                foreach ($method['params'] as $val) {
                    $require = $val['require'] ? '是' : '否';
                    $content .= "|{$val['name']}|{$require}|{$val['desc']}|{$val['type']}|{$val['default']}|\n";
                }
                $content .= "\n";
            }
        }
        // 保存文档
        $path = base_path("plugin/{$name}/docs");
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        file_put_contents($path . '/events.md', $content);
        $output->writeln("<info>Generate Plugin {$name} event docs success</info>");
    }

    /**
     * 获取事件数据
     * @param string $name
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getEvents(string $name)
    {
        $data = glob(base_path("plugin/{$name}/app/event/*Event.php"));
        if (empty($data)) {
            return [];
        }
        $config = config('plugin.hg.apidoc.app');
        $i      = 0;
        $events = [];
        foreach ($data as $path) {
            $fileName = basename($path, '.php');
            $class    = "plugin\\{$name}\\app\\event\\{$fileName}";
            if (!class_exists($class)) {
                continue;
            }
            // 反射类
            $refClass = new \ReflectionClass($class);
            // 类注释
            $classText  = ParseAnnotation::parseTextAnnotation($refClass);
            $classTitle = $classText[0] ?? $class;
            unset($classText[0]);
            $events[$i]['title']     = $classTitle;
            $events[$i]['name']      = $fileName;
            $events[$i]['namespace'] = $class;
            $events[$i]['desc']      = implode("，", $classText);
            $events[$i]['methods']   = [];
            // 获取类方法
            $methods = $refClass->getMethods();
            foreach ($methods as $methodRef) {
                // 反射方法
                $refMethod = $refClass->getMethod($methodRef->name);
                // 方法注释
                $methodText = ParseAnnotation::parseTextAnnotation($refMethod);
                // 方法参数
                $classAnnotations = (new ParseAnnotation($config))->getMethodAnnotation($refMethod);
                if (empty($classAnnotations)) {
                    continue;
                }
                $methodTitle = $methodText[0] ?? '未知方法';
                $methodName  = $methodRef->name;
                unset($methodText[0]);
                $methodDesc = implode("\n", $methodText);
                $params     = $classAnnotations['param'] ?? [];
                $params     = $this->is_array_2d($params) ? $params : [$params];
                $returned   = $classAnnotations['returned'] ?? [];
                $returned   = $this->is_array_2d($returned) ? $returned : [$returned];
                // 保存数据
                $events[$i]['methods'][] = [
                    'title' => $methodTitle,
                    'name' => $methodName,
                    'desc' => $methodDesc,
                    'params' => $params,
                ];
            }
            $i++;
        }
        return $events;
    }

    /**
     * 判断是否是二维数组
     * @param mixed $array
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function is_array_2d($array)
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                return false; // 发现不是数组，返回false表示是一维数组
            }
        }
        return true; // 所有元素都是数组，返回true表示是二维数组
    }
}
