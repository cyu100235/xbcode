<?php
namespace app\command;

use hg\apidoc\parses\ParseAnnotation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 服务接口文档命令
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbPluginServiceDocCommand extends Command
{
    // 命令名称
    protected static $defaultName = 'xb-plugin:service-doc';

    // 命令描述
    protected static $defaultDescription = 'Xb Plugin service doc';

    /**
     * 配置
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Xb plugin name');
        $this->addArgument('class', InputArgument::OPTIONAL, 'Xb plugin class');
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
        $className = $input->getArgument('class');
        if ($className) {
            // 生成指定服务接口类
            $this->generateClass($input, $output);
        } else {
            // 生成服务接口文档
            $this->generateDoc($input, $output);
        }
        // 返回结果
        return self::SUCCESS;
    }

    /**
     * 生成服务接口类
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function generateClass(InputInterface $input, OutputInterface $output)
    {
        $pluginName = $input->getArgument('name');
        $className  = $input->getArgument('class');
        $template = base_path('app/common/data/template/PluginService.tpl');
        if (!file_exists($template)) {
            $output->writeln("<error>Template file not exists</error>");
            return;
        }
        $className = ucfirst($className);
        $path = base_path("plugin/{$pluginName}/service/{$className}.php");
        if (file_exists($path)) {
            $output->writeln("<error>File {$path} already exists</error>");
            return;
        }
        $content = file_get_contents($template);
        $content = str_replace('{PLUGIN_NAME}', $pluginName, $content);
        $content = str_replace('{CLASS_NAME}', $className, $content);
        file_put_contents($path, $content);
        $output->writeln("<info>Generate Plugin {$pluginName} service {$className} success</info>");
    }

    /**
     * 生成服务接口文档
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function generateDoc(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $data = $this->getApis($name);
        // 生成文档
        $content = "# 服务接口文档\n\n";
        foreach ($data as $class) {
            // 生成类标题
            $content .= "## 服务类名：{$class['title']}\n\n";
            $content .= "### 命名空间：{$class['namespace']}\n\n";
            // 类描述
            if ($class['desc']) {
                $content .= "{$class['desc']}\n\n";
            }
            if (empty($class['methods'])) {
                continue;
            }
            foreach ($class['methods'] as $method) {
                // 方法名称
                $content .= "## {$method['title']}\n\n";
                $content .= "### {$method['name']}\n\n";
                // 方法描述
                if ($method['desc']) {
                    $content .= "{$method['desc']}\n\n";
                }
                // 调用方式
                $content .= "#### 调用方式\n\n";
                $content .= "```php\n\n";
                $content .= "\\{$class['namespace']}::{$method['name']}(";
                $params  = [];
                foreach ($method['params'] as $val) {
                    $params[] = "{$val['type']} \${$val['name']}";
                }
                $content .= implode(", ", $params);
                $content .= ");\n";
                $content .= "\n```\n\n";
                // 拼接参数
                $content .= "#### 方法参数\n\n";
                $content .= "| 参数名 | 说明 | 类型 | 默认值  |";
                $content .= "\n";
                $content .= "| ----- | ----- | ----- | ----- |";
                $content .= "\n";
                foreach ($method['params'] as $val) {
                    $default = $val['default'] ?? '';
                    $content .= "| {$val['name']} | {$val['desc']} | {$val['type']} | {$default} |\r\n";
                }
                $content .= "\n\n\n";
            }
        }
        // 保存文档
        $path = base_path("plugin/{$name}/docs");
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        file_put_contents($path . '/apis.md', $content);
        $output->writeln("<info>Generate Plugin {$name} api docs success</info>");
    }

    /**
     * 获取事件数据
     * @param string $name
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function getApis(string $name)
    {
        $apis = glob(base_path("plugin/{$name}/service/*.php"));
        if (empty($apis)) {
            return [];
        }
        $i      = 0;
        $data   = [];
        $config = config('plugin.hg.apidoc.app.apidoc', []);
        foreach ($apis as $path) {
            $fileName = basename($path, '.php');
            $class    = "plugin\\{$name}\\service\\{$fileName}";
            if (!class_exists($class)) {
                continue;
            }
            // 反射类
            $refClass = new \ReflectionClass($class);
            // 类注释
            $classText = ParseAnnotation::parseTextAnnotation($refClass);
            // 类标题
            $classTitle = $classText[0] ?? $class;
            unset($classText[0]);
            $data[$i]['title']     = $classTitle;
            $data[$i]['name']      = $fileName;
            $data[$i]['namespace'] = $class;
            $data[$i]['desc']      = implode("，", $classText);
            $data[$i]['methods']   = [];
            // 获取类方法
            $methods = $refClass->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_STATIC);
            foreach ($methods as $methodRef) {
                // 必须是公共方法同时是静态方法
                if (!$methodRef->isPublic() || !$methodRef->isStatic()) {
                    continue;
                }
                // 反射方法
                $refMethod = $refClass->getMethod($methodRef->name);
                // 方法注释
                $methodText = ParseAnnotation::parseTextAnnotation($refMethod, true);
                if (empty($methodText)) {
                    continue;
                }
                // 方法注解
                $classAnnotations = (new ParseAnnotation($config))->getMethodAnnotation($refMethod);
                if (empty($classAnnotations)) {
                    // 自定义注解
                    $classAnnotations = $this->parseMethodAnnotation($methodText);
                }
                $description = $this->description($methodText);
                $methodTitle = $methodText[0] ?? '未知方法标题';
                $methodName  = $methodRef->name;
                $params      = $classAnnotations['param'] ?? [];
                $params      = $this->is_array_2d($params) ? $params : [$params];
                $returned    = $classAnnotations['returned'] ?? [];
                $returned    = $this->is_array_2d($returned) ? $returned : [$returned];
                // 保存数据
                $data[$i]['methods'][] = [
                    'title'  => $methodTitle,
                    'name'   => $methodName,
                    'desc'   => $description,
                    'params' => $params,
                ];
            }
            $i++;
        }
        return $data;
    }

    /**
     * 获取描述
     * @param array $data
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function description(array $data)
    {
        $temp = '';
        foreach ($data as $str) {
            if (strpos($str, '@') !== false) {
                break;
            }
            $temp = $str;
        }
        return $temp;
    }

    /**
     * 解析方法注解
     * @param array $params
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function parseMethodAnnotation(array $params)
    {
        if (empty($params)) {
            return [];
        }
        unset($params[0]);
        // 方法参数
        $data = [];
        foreach ($params as $methodAttr) {
            // 解析参数
            if (strpos($methodAttr, '@param') !== false) {
                $attrs = str_replace('@param ', '', $methodAttr);
                $attrs = explode(' ', $attrs);
                if (empty($attrs)) {
                    continue;
                }
                $tempType   = $attrs[0] ?? '';
                $tempName   = $attrs[1] ?? '';
                $tempName   = str_replace('$', '', $tempName);
                $methodDesc = '';
                if (!empty($attrs[2])) {
                    $methodDesc = $attrs[2];
                }
                $data[] = [
                    'type' => $tempType,
                    'name' => $tempName,
                    'desc' => $methodDesc,
                ];
            }
        }
        return [
            'param' => $data
        ];
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
