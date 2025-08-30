<?php
namespace plugin\xbDeveloper\utils;

use Exception;
use support\Log;

/**
 * GIT工具类
 * @copyright 贵州积木云网络网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class GitUtil
{
    /**
     * 克隆仓库
     * @param string $url 仓库地址
     * @param string $targetPath 克隆路径
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function clone(string $url, string $targetPath)
    {
        // 验证函数是否开启
        self::verifyExec('shell_exec');
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0777, true);
        }
        // 转换路径
        $url = escapeshellcmd($url);
        // 克隆命令
        $command = "git clone {$url} {$targetPath} 2>&1";
        // 执行命令
        $output = shell_exec($command);
        if (empty($output)) {
            throw new Exception("克隆失败，请检查仓库地址");
        }
    }

    /**
     * 验证函数是否开启
     * @param string $funName
     * @return void
     * @copyright 贵州积木云网络网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function verifyExec(string $funName = 'exec')
    {
        if (!function_exists($funName)) {
            throw new Exception("应用插件操作，未开启【{$funName}】函数");
        }
    }
}
