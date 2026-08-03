<?php
namespace plugin\xbCode\api;

use Exception;

/**
 * 输出日志接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class LogApi
{
    /**
     * 输出日志
     * @param string $message 输出消息
     * @param string $type 输出类型：info, error, warning
     * @param array $option 输出选项
     * - bool $is_write_log 是否写入日志文件
     * - string $title 日志文件标题，默认为“温馨提示”
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function output(string $message, string $type = 'info', array $option = [])
    {
        // 类型转大写
        $type = strtoupper($type);
        // 是否在开发模式
        $isDebug = DebugApi::status();
        // 是否写入日志文件
        $isWriteLog = true;
        if (isset($option['is_write_log']) && $option['is_write_log'] === false) {
            $isWriteLog = false;
        }
        // 日志文件标题
        $title = $option['title'] ?? '温馨提示';
        // 检查日志类型
        if (!in_array($type, ['INFO', 'ERROR', 'WARNING'])) {
            throw new Exception("日志类型错误");
        }
        // 判断是否在开发模式
        if ($isDebug) {
            // 开发模式下输出至控制台
            self::console($message, $type);
        }
        // 写入日志文件
        if ($isWriteLog) {
            self::addLog($title, $message);
        }
    }

    /**
     * 输出至控制台
     * @param string $message 输出消息
     * @param string $type 类型
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
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
     * @param string $title 输出标题
     * @param string $content 输出内容
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function addLog(string $title, string $content)
    {
        $date = date('Y-m-d');
        $composerLogPath = base_path("/runtime/logs/xb_{$date}.log");
        if (!is_dir(dirname($composerLogPath))) {
            mkdir(dirname($composerLogPath), 0777, true);
        }
        $dateTime = date('Y-m-d H:i:s');
        $message = "【{$dateTime}】 {$title}\n{$content}\n";
        file_put_contents($composerLogPath, $message, FILE_APPEND);
    }
}