<?php
namespace plugin\xbCode\api;

use Exception;

/**
 * Composer接口
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Composer
{
    /**
     * 安装composer依赖
     * @param string $name
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function install(string $name = '')
    {
        // 获取composer依赖
        $data = static::getComposerPackages($name);
        foreach ($data as $package) {
            try {
                // 执行composer包安装
                $result = static::installComposerPackage($package);
            } catch (\Throwable $th) {
                static::addLog("{$package} 安装失败", $th->getMessage());
                static::output("{$package} 安装失败，请查看安装日志", 'error');
                continue;
            }
            // 记录安装日志
            if ($result['state']) {
                static::output("{$package} 安装成功");
            }else{
                static::addLog($result['command'], $result['output']);
                static::output("{$package} 安装失败，请查看安装日志", 'error');
            }
        }
    }

    /**
     * 获取composer依赖
     * @param string $name
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getComposerPackages(string $name = '')
    {
        $data = [];
        if (empty($name)) {
            // 获取全部插件composer
            $plugins = glob(base_path() . '/plugin/*/plugins.json');
            $data = [];
            foreach ($plugins as $path) {
                $plugin = file_get_contents($path);
                if (empty($plugin)) {
                    continue;
                }
                $plugin = json_decode($plugin, true);
                if (empty($plugin['composer'])) {
                    continue;
                }
                $data = array_merge($data, $plugin['composer']);
            }
        }else{
            $plugin = base_path() . "/plugin/{$name}/plugins.json";
            if (!file_exists($plugin)) {
                return $data;
            }
            $plugin = file_get_contents($plugin);
            if (empty($plugin)) {
                return $data;
            }
            $plugin = json_decode($plugin, true);
            if (empty($plugin['composer'])) {
                return $data;
            }
            $data = $plugin['composer'] ?? [];
        }
        return $data;
    }

    /**
     * 执行composer包安装
     * @param string $package
     * @throws \Exception
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function installComposerPackage(string $package)
    {
        // 确保composer命令存在
        $composerPath = trim(shell_exec('which composer'));
        if (empty($composerPath)) {
            throw new Exception('请先安装 Composer');
        }
        // 确保exec命令存在
        if (!function_exists('exec')) {
            throw new Exception('请先解除 exec 函数禁用');
        }
        // 构建composer require命令
        $command = "{$composerPath} require {$package} -n 2>&1";
        
        // 执行命令
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);
        
        // 返回执行结果
        return [
            'state' => $returnVar === 0,
            'output' => implode("\n", $output),
            'command' => $command
        ];
    }

    /**
     * 输出至控制台
     * @param string $message 输出消息
     * @param string $type 类型
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function output(string $message, string $type = 'info')
    {
        $message = date('Y-m-d H:i:s') . ' [' . strtoupper($type) . '] ' . $message . "\n";
        echo $message;
    }

    /**
     * 记录日志
     * @param string $title
     * @param string $content
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function addLog(string $title,string $content)
    {
        $date = date('Y-m-d');
        $dateTime = date('Y-m-d H:i:s');
        $composerLogPath = base_path() . "/runtime/logs/composer/{$date}/{$dateTime}.log";
        if (!is_dir(dirname($composerLogPath))) {
            mkdir(dirname($composerLogPath), 0777, true);
        }
        $message = "【{$dateTime}】 {$title}\n{$content}\n";
        file_put_contents($composerLogPath, $message, FILE_APPEND);
    }
}