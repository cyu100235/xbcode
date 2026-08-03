<?php
namespace plugin\xbCrontab\api;

use Exception;

/**
 * 任务执行接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class TaskApi
{
    /**
     * 创建任务执行类实例
     * @return TaskApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 获取任务执行类方法列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getMethods()
    {
        return [
            '10' => 'shell',
            '20' => 'url',
            '30' => 'php',
        ];
    }

    /**
     * 执行shell命令
     * @param string $command
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function shell(string $command)
    {
        // 设置工作目录
        $workDir = base_path();
        chdir($workDir);
        // 执行shell命令并且输出结果
        $output = shell_exec($command);
        if ($output === false) {
            throw new Exception("执行shell命令 {$command} 失败");
        }
    }

    /**
     * 执行url请求
     * @param string $url
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function url(string $url)
    {
        $response = file_get_contents($url);
        if ($response === false) {
            throw new Exception("请求URL {$url} 失败");
        }
    }

    /**
     * 执行php代码
     * @param string $code
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function php(string $code)
    {
        try {
            // 执行php代码
            $result = eval ($code);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}