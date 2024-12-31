<?php
namespace app\command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 创建小白插件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbPluginCreateCommand extends Command
{
    protected static $defaultName        = 'xb-plugin:create';
    protected static $defaultDescription = 'Xb Plugin Create';

    /**
     * 目录备注
     * @var array
     */
    protected static $dirRemarks = [
        'api'               => "接口目录\n主要与其他插件对接，非网络请求接口",
        'app'               => '应用目录',
        'app/controller'    => '控制器目录',
        'app/event'         => '事件目录',
        'app/model'         => '模型目录',
        'app/view'          => '视图目录',
        'app/view/index'    => '首页视图目录',
        'config'            => '配置目录',
        'data'              => '数据目录',
        'data/config'       => '数据模板目录',
        'data/sql'          => 'SQL目录',
        'public'            => '公共目录',
        'view'              => 'view视图目录',
        'setting'           => '设置目录',
        'docs'              => '文档目录',
    ];

    /**
     * 插件文件路径
     * @var array
     */
    protected static $files = [
        'api/Install.php',
        'app/functions.php',
        'app/controller/IndexController.php',
        'app/view/index/index.html',
        'config/apidoc.php',
        'config/app.php',
        'config/autoload.php',
        'config/container.php',
        'config/exception.php',
        'config/log.php',
        'config/middleware.php',
        'config/process.php',
        'config/redis.php',
        'config/route.php',
        'config/slot.php',
        'config/static.php',
        'config/thinkorm.php',
        'config/translation.php',
        'config/view.php',
        'data/sql/install.sql',
        'data/sql/update.sql',
        'data/sql/uninstall.sql',
        'data/config/menus.php',
        'data/config/dict.php',
        'setting/basis.php',
        'view/workbench.vue',
    ];

    /**
     * 配置命令
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Xb plugin name');
    }

    /**
     * 执行命令
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = (string)$input->getArgument('name');
        $output->writeln("Create Xb Plugin $name");

        if (strpos($name, '/') !== false) {
            $output->writeln('<error>Bad name, name must not contain character \'/\'</error>');
            return self::FAILURE;
        }

        // Create dir config/plugin/$name
        if (is_dir($plugin_config_path = base_path() . "/plugin/{$name}")) {
            $output->writeln("<error>Dir $plugin_config_path already exists</error>");
            return self::FAILURE;
        }
        $this->createAll($name);
        return self::SUCCESS;
    }
    
    /**
     * 创建所有文件
     * @param string $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function createAll(string $name)
    {
        try {
            $base_path = base_path();
            // 创建目录
            foreach (self::$dirRemarks as $dir => $content) {
                $path = "{$base_path}/plugin/{$name}/{$dir}";
                $this->mkdir($path, $content, $name);
            }
            // 创建文件
            foreach (self::$files as $path) {
                $this->createFile($path, $name);
            }
        } catch (\Throwable $th) {
            echo $th->getMessage();
            echo "\r\n";
        }
    }

    /**
     * 创建目录
     * @param string $path
     * @param string $content
     * @param string $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function mkdir(string $path, string $content, string $name)
    {
        if (is_dir($path)) {
            return;
        }
        echo "Create Dir $path\r\n";
        mkdir($path, 0775, true);
        file_put_contents("{$path}/remarks.txt", "Create by 小白基地\n\n{$content}");
    }

    /**
     * 创建文件
     * @param string $path
     * @param string $name
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function createFile(string $path, string $name)
    {
        // 站点根路径
        $basePath = base_path();
        // 文件后缀
        $suffix = ['.php', '.html', '.md', '.sql', '.json','.vue'];
        // 去除后缀
        $fileName = str_replace($suffix, '', $path);
        // 模板路径
        $tplPath = "/extend/xbcode/data/plugin/{$fileName}.tpl";
        // 完整模板路径
        $tplFullPath = "{$basePath}{$tplPath}";
        if (!file_exists($tplFullPath)) {
            throw new Exception("Template {$tplPath} not exists");
        }
        // 读取模板内容
        $content = file_get_contents($tplFullPath);
        $content = str_replace('{PLUGIN_NAME}', $name, $content);
        // 目标文件路径
        $targetPath = "{$basePath}/plugin/{$name}/{$path}";
        echo "Create File $targetPath\r\n";
        // 写入文件
        file_put_contents($targetPath, $content);
    }
}
