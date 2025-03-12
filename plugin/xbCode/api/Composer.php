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
                LogApi::output("{$package} 依赖安装失败，{$th->getMessage()}", $th->getMessage(), true, 'error');
                continue;
            }
            // 记录安装日志
            if ($result['state']) {
                LogApi::output("{$package} 依赖安装成功");
            }else{
                LogApi::output("{$package} 依赖安装失败，请查看安装日志", "{$result['command']}\n{$result['output']}", true, 'error');
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
        // 确保shell_exec命令存在
        if (!function_exists('shell_exec')) {
            throw new Exception('请先解除 shell_exec 函数禁用');
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
}