<?php
namespace plugin\xbCode\command;

use Exception;
use plugin\xbCode\api\Composer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 安装composer依赖
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginInit extends Command
{
    protected static $defaultName = 'xb-plugin:init';
    protected static $defaultDescription = 'Xb Plugin init Install';

    /**
     * 配置命令
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected function configure()
    {
        // $this->addArgument('name', InputArgument::REQUIRED, 'Xb plugin name');
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
        try {
            //检查禁用函数
            $this->checkDisabledFunction();
            // 检查扩展
            $this->checkExtension();
        } catch (\Throwable $th) {
            $output->writeln("<fg=red>{$th->getMessage()}</fg=red>");
            return self::FAILURE;
        }

        // 复制nginx文件
        $nginxPath = dirname(__DIR__) . '/nginx.conf';
        if (file_exists($nginxPath)) {
            copy($nginxPath, base_path() . '/nginx.conf');
            $output->writeln("复制NGINX规则文件完成...");
        }
        // 复制README.md文件
        $readmePath = dirname(__DIR__) . '/README.md';
        if (file_exists($readmePath)) {
            copy($readmePath, base_path() . '/README.md');
            $output->writeln("复制README文件完成...");
        }
        // 安装中间件
        $this->copyMiddleware();
        // 替换TP-ORM
        $ormShortPath = '/config/think-orm.php';
        $ormPath = dirname(__DIR__) . $ormShortPath;
        if (file_exists($ormPath)) {
            copy($ormPath, base_path() . $ormShortPath);
            $output->writeln("替换TP-ORM配置文件完成...");
        }
        // 替换TP-Cache
        $cacheShortPath = '/config/think-cache.php';
        $cachePath = dirname(__DIR__) . $cacheShortPath;
        if (file_exists($cachePath)) {
            copy($cachePath, base_path() . $cacheShortPath);
            $output->writeln("替换TP-Cache配置文件完成...");
        }
        // 替换REDIS配置文件
        $redisPath = dirname(__DIR__) . '/config/redis.php';
        if (file_exists($redisPath)) {
            copy($redisPath, base_path() . '/config/redis.php');
            $output->writeln("替换REDIS配置文件完成...");
        }
        // 替换主进程配置文件
        $mainPath = dirname(__DIR__) . '/data/process.php';
        if (file_exists($mainPath)) {
            copy($mainPath, base_path() . '/config/process.php');
            $output->writeln("替换process配置文件完成...");
        }
        // 替换gitignore文件
        $gitignorePath = dirname(__DIR__) . '/data/gitignore.tpl';
        if (file_exists($gitignorePath)) {
            copy($gitignorePath, base_path() . '/.gitignore');
            $output->writeln("替换.gitignore文件完成...");
        }
        // 安装所有依赖
        Composer::install();
        // 安装完成
        $output->writeln("<fg=green>全部插件依赖安装完成...</fg=green>");
        // 返回完成
        return self::SUCCESS;
    }
    
    /**
     * 安装插件中间件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function copyMiddleware()
    {
        $content = <<<STR
<?php
use plugin\\xbCode\\utils\\MiddlewareUtil;

\$middlewares = MiddlewareUtil::modules();

return \$middlewares;
STR;
        $path = base_path() . '/config/middleware.php';
        file_put_contents($path, $content);
        echo "复制中间件配置文件完成...\n";
    }

    /**
     * 检查禁用函数
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function checkDisabledFunction()
    {
        $disabledPath = dirname(__DIR__) . '/app/install/config/fun.php';
        if (!file_exists($disabledPath)) {
            throw new Exception('禁用函数配置文件不存在');
        }
        $data = include $disabledPath;
        $disabled = [];
        foreach ($data as $value) {
            if (!$value['name']) {
                throw new Exception('禁用函数配置错误');
            }
            if (!function_exists($value['name'])) {
                $disabled[] = $value['name'];
            }
        }
        if ($disabled) {
            $disabled = implode(',', $disabled);
            throw new Exception("【{$disabled}】 函数被禁用，请解除函数禁用");
        }
    }

    /**
     * 检查扩展
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function checkExtension()
    {
        $extraPath = dirname(__DIR__) . '/app/install/config/extra.php';
        if (!file_exists($extraPath)) {
            throw new Exception('扩展配置文件不存在');
        }
        $data = include $extraPath;
        $list = [];
        foreach ($data as $value) {
            if (!$value['name']) {
                throw new Exception('扩展标识配置错误');
            }
            if (!extension_loaded($value['name'])) {
                $list[] = $value['name'];
            }
        }
        if ($list) {
            $extras = implode(',', $list);
            throw new Exception("【{$extras}】 扩展未安装，请安装扩展");
        }
    }
}
