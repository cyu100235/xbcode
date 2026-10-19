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
     * @return string 命令输出内容
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
        return trim((string) $output);
    }

    /**
     * 执行url请求
     * @param string $url
     * @return string 请求响应内容
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function url(string $url)
    {
        $response = file_get_contents($url);
        if ($response === false) {
            throw new Exception("请求URL {$url} 失败");
        }
        return trim((string) $response);
    }

    /**
     * 执行php代码
     * @param string $code
     * @return string 代码输出或返回值
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function php(string $code)
    {
        // 开启输出缓冲，捕获代码内 echo 的内容
        ob_start();
        try {
            // 执行php代码
            $result = eval($code);
        } catch (\Throwable $th) {
            // 出错时丢弃缓冲内容
            ob_end_clean();
            throw new Exception($th->getMessage());
        }
        $output = ob_get_clean();
        // 优先返回输出内容
        if ($output !== false && trim((string) $output) !== '') {
            return trim((string) $output);
        }
        // 其次返回代码返回值
        if (is_string($result) || is_numeric($result)) {
            return trim((string) $result);
        }
        if (is_array($result) || is_object($result)) {
            return trim((string) json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        return '';
    }
}