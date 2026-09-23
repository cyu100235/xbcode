<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use RuntimeException;

/**
 * 插件 Composer 依赖管理器
 *
 * 依赖声明来自各插件 plugin.json 的 composer 节点，格式为「类名 => 包名(:版本约束)」，例如：
 *   "Webman\\RedisQueue\\Client" : "webman/redis-queue"
 *   "Webman\\Log\\Middleware"    : "webman/log:~2.1"
 *
 * 对外 API：
 *   Composer::install($context['composer']);            // 安装尚未安装的依赖（composer require）
 *   Composer::update($context['composer']);             // 更新已安装的依赖（composer update）
 *   Composer::uninstall($context['composer'], 'xbAdmin');// 卸载依赖（composer remove）
 *
 * 说明：
 *   - 是否已安装以 vendor/composer/installed.json 为准，声明里的类名仅作语义标注，不参与判定；
 *   - 卸载时会扫描其它插件的 plugin.json，仍被引用的包自动跳过，避免卸载插件把公共依赖删掉；
 *   - 命令执行失败（进程无法启动或返回码非 0）统一抛 RuntimeException，由调用方决定是否中断流程；
 *   - composer 可执行文件优先取项目根目录的 composer.phar，找不到则使用 PATH 中的 composer 命令。
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Composer
{
    /**
     * 安装依赖（仅安装尚未安装的包）
     * @param array $composer 插件 composer 声明（类名 => 包名:版本约束）
     * @return array<int,string> 实际执行安装的包列表
     * @throws RuntimeException
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function install(array $composer): array
    {
        $missing = [];
        foreach (self::packages($composer) as $package) {
            if (!self::isInstalled($package['name'])) {
                $missing[] = $package['declare'];
            }
        }
        if (empty($missing)) {
            return [];
        }
        // 一次性安装所有缺失依赖
        self::run(array_merge(['require'], $missing));
        return $missing;
    }

    /**
     * 更新依赖（仅更新已安装的包）
     * @param array $composer 插件 composer 声明（类名 => 包名:版本约束）
     * @return array<int,string> 实际执行更新的包列表
     * @throws RuntimeException
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function update(array $composer): array
    {
        $installed = [];
        foreach (self::packages($composer) as $package) {
            if (self::isInstalled($package['name'])) {
                $installed[] = $package['name'];
            }
        }
        if (empty($installed)) {
            return [];
        }
        // 一次性更新已有依赖
        self::run(array_merge(['update'], $installed));
        return $installed;
    }

    /**
     * 卸载依赖（仍被其它插件声明的包会自动跳过）
     * @param array  $composer   插件 composer 声明（类名 => 包名:版本约束）
     * @param string $pluginName 当前插件目录名称，用于排除自身后扫描其它插件
     * @return array<int,string> 实际执行卸载的包列表
     * @throws RuntimeException
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function uninstall(array $composer, string $pluginName = ''): array
    {
        // 其它插件仍在使用的包不能卸载
        $inUse = self::usedByOtherPlugins($pluginName);
        $remove = [];
        foreach (self::packages($composer) as $package) {
            if (isset($inUse[$package['name']])) {
                continue;
            }
            if (!self::isInstalled($package['name'])) {
                continue;
            }
            $remove[] = $package['name'];
        }
        if (empty($remove)) {
            return [];
        }
        // 一次性卸载无用依赖
        self::run(array_merge(['remove'], $remove));
        return $remove;
    }

    /* ---------------------------- 内部能力（不对外暴露） ---------------------------- */

    /**
     * 规整依赖声明为「包名 + 原始声明」列表
     * @param array $composer 插件 composer 声明（类名 => 包名:版本约束）
     * @return array<string,array{name:string,declare:string}> 以包名为键，天然去重
     */
    protected static function packages(array $composer): array
    {
        $packages = [];
        foreach ($composer as $declare) {
            if (!is_string($declare) || trim($declare) === '') {
                continue;
            }
            $declare = trim($declare);
            // 包名 = 版本约束之前的部分，如 webman/log:~2.1 => webman/log
            $name = trim(explode(':', $declare)[0]);
            if ($name === '') {
                continue;
            }
            $packages[$name] = ['name' => $name, 'declare' => $declare];
        }
        return $packages;
    }

    /**
     * 判断依赖包是否已安装
     * @param string $package 包名
     * @return bool
     */
    protected static function isInstalled(string $package): bool
    {
        $installed = self::installed();
        return isset($installed[strtolower($package)]);
    }

    /**
     * 读取已安装依赖包清单（vendor/composer/installed.json）
     * @return array<string,bool> 以小写包名为键
     */
    protected static function installed(): array
    {
        $file = base_path('vendor/composer/installed.json');
        if (!is_file($file)) {
            return [];
        }
        $config = json_decode((string) file_get_contents($file), true);
        if (!is_array($config)) {
            return [];
        }
        // composer 2 为 {"packages":[...]}，composer 1 直接是包列表
        $list = $config['packages'] ?? $config;
        $installed = [];
        foreach ($list as $item) {
            if (!empty($item['name']) && is_string($item['name'])) {
                $installed[strtolower($item['name'])] = true;
            }
        }
        return $installed;
    }

    /**
     * 收集其它插件仍声明的依赖包
     * @param string $pluginName 当前插件目录名称（扫描时排除）
     * @return array<string,bool> 以小写包名为键
     */
    protected static function usedByOtherPlugins(string $pluginName): array
    {
        $used = [];
        foreach (glob(base_path('plugin/*/plugin.json')) ?: [] as $file) {
            if ($pluginName !== '' && basename(dirname($file)) === $pluginName) {
                continue;
            }
            $config = json_decode((string) file_get_contents($file), true);
            if (!is_array($config)) {
                continue;
            }
            foreach (self::packages($config['composer'] ?? []) as $package) {
                $used[strtolower($package['name'])] = true;
            }
        }
        return $used;
    }

    /**
     * composer 可执行命令
     * @return string
     */
    protected static function binary(): string
    {
        // 优先使用项目根目录的 composer.phar
        $phar = base_path('composer.phar');
        if (is_file($phar)) {
            return escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($phar);
        }
        // 回退到 PATH 中的 composer
        return 'composer';
    }

    /**
     * 执行 composer 命令（在项目根目录下运行）
     * @param array<int,string> $args 命令参数
     * @return void
     * @throws RuntimeException 进程无法启动或返回码非 0
     */
    protected static function run(array $args): void
    {
        $args[] = '--no-interaction';
        $command = self::binary() . ' ' . implode(' ', array_map('escapeshellarg', $args));
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = proc_open($command, $descriptors, $pipes, base_path());
        if (!is_resource($process)) {
            throw new RuntimeException('无法启动 composer 进程，请确认服务器已安装 composer');
        }
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $exitCode = (int) proc_close($process);
        if ($exitCode !== 0) {
            $message = trim($stderr . "\n" . $stdout);
            throw new RuntimeException("composer 执行失败（exit={$exitCode}）：{$message}");
        }
    }
}