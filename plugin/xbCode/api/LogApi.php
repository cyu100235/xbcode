<?php
namespace plugin\xbCode\api;

use Exception;

/**
 * 输出日志接口
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class LogApi
{
    /**
     * 输出日志
     * @param string $message 输出消息
     * @param string $error 错误消息
     * @param bool $console 是否输出至控制台
     * @param string $type 类型：info, error, warning
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function output(string $message, string $error = '', bool $console = true, string $type = 'info')
    {
        // 类型转大写
        $type = strtoupper($type);
        // 检查日志类型
        if (!in_array($type, ['INFO', 'ERROR', 'WARNING'])) {
            throw new Exception("日志类型错误");
        }
        // 输出至控制台
        if ($console) {
            self::console($message, $type);
        }
        // 输出错误日志到文件
        if ($error) {
            self::addLog($message, $error);
        }
    }

    /**
     * 输出至控制台
     * @param string $message 输出消息
     * @param string $type 类型
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function console(string $message, string $type = 'INFO')
    {
        $dateTime = date('Y-m-d H:i:s');
        $message = "{$dateTime} [{$type}] {$message}";
        if ($type == 'ERROR') {
            // 红色
            $message = "\033[31m{$message}\033[0m";
        }
        if ($type == 'WARNING') {
            // 黄色
            $message = "\033[33m{$message}\033[0m";
        }
        echo $message;
        echo PHP_EOL;
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
        $composerLogPath = base_path() . "/runtime/logs/xbCode/{$date}.log";
        if (!is_dir(dirname($composerLogPath))) {
            mkdir(dirname($composerLogPath), 0777, true);
        }
        $dateTime = date('Y-m-d H:i:s');
        $message = "【{$dateTime}】 {$title}\n{$content}\n";
        file_put_contents($composerLogPath, $message, FILE_APPEND);
    }
}