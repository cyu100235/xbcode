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
 *   - composer 可执行文件优先取项目根目录的 composer.phar，找不到则使用 PATH 中的 composer 命令；
 *   - 执行过程实时回显 composer 输出，完整日志写入 runtime/composer.log，超时（默认 600 秒）自动终止。
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Composer
{
    /**
     * composer 命令执行超时时间（秒），超时后终止进程并抛异常
     */
    protected const TIMEOUT = 600;

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
            // 已安装的依赖直接跳过，仅提示
            if (self::isInstalled($package['name'])) {
                echo $package['name'] . ' 包已安装' . PHP_EOL;
                flush();
                continue;
            }
            $missing[] = $package['declare'];
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
     *
     * 子进程输出重定向到 runtime/composer.log 并边执行边回显，既保证长任务有进度反馈，
     * 也避免管道缓冲区写满导致的死锁；超过 TIMEOUT 秒自动终止进程。
     * @param array<int,string> $args 命令参数
     * @return void
     * @throws RuntimeException 进程无法启动、执行超时或返回码非 0
     */
    protected static function run(array $args): void
    {
        $args[] = '--no-interaction';
        $command = self::binary() . ' ' . implode(' ', array_map('escapeshellarg', $args));
        // 输出落盘再增量读取，兼容 Windows 与 Linux
        $logFile = runtime_path() . DIRECTORY_SEPARATOR . 'composer.log';
        file_put_contents($logFile, '');
        $devNull = DIRECTORY_SEPARATOR === '\\' ? 'nul' : '/dev/null';
        $descriptors = [
            0 => ['file', $devNull, 'r'],
            1 => ['file', $logFile, 'a'],
            2 => ['file', $logFile, 'a'],
        ];
        $process = proc_open($command, $descriptors, $pipes, base_path());
        if (!is_resource($process)) {
            throw new RuntimeException('无法启动 composer 进程，请确认服务器已安装 composer');
        }
        $offset = 0;
        $output = self::tail($logFile, $offset);
        $expire = time() + self::TIMEOUT;
        $exitCode = null;
        while (true) {
            $status = proc_get_status($process);
            if (!$status['running']) {
                $exitCode = (int) $status['exitcode'];
                break;
            }
            if (time() >= $expire) {
                proc_terminate($process);
                break;
            }
            // 轮询等待，期间持续回显 composer 进度
            usleep(200000);
            $output .= self::tail($logFile, $offset);
        }
        $output .= self::tail($logFile, $offset);
        proc_close($process);
        if ($exitCode === null) {
            throw new RuntimeException(sprintf(
                'composer 执行超时（超过 %d 秒已终止）：%s（完整日志：runtime/composer.log）',
                self::TIMEOUT,
                trim(substr($output, -2000))
            ));
        }
        if ($exitCode !== 0) {
            throw new RuntimeException(sprintf(
                'composer 执行失败（exit=%d）：%s（完整日志：runtime/composer.log）',
                $exitCode,
                trim(substr($output, -2000))
            ));
        }
    }

    /**
     * 读取日志文件的新增内容并实时回显
     * @param string $file   日志文件
     * @param int    $offset 已读取的字节数，读取后自动累加
     * @return string 本次新增的内容
     */
    protected static function tail(string $file, int &$offset): string
    {
        $handle = @fopen($file, 'r');
        if (!$handle) {
            return '';
        }
        fseek($handle, $offset);
        $chunk = (string) stream_get_contents($handle);
        fclose($handle);
        $offset += strlen($chunk);
        if ($chunk !== '') {
            echo $chunk;
            flush();
        }
        return $chunk;
    }
}