<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\utils;

use Exception;

/**
 * GIT工具类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class GitUtil
{
    /**
     * 克隆仓库
     * @param string $url 仓库地址
     * @param string $targetPath 克隆路径
     * @param string $branch 分支名称
     * @throws \Exception
     * @return string|bool|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function clone(string $url, string $targetPath, string $branch = '')
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
        if (!empty($branch)) {
            $command = "git clone -b {$branch} {$url} {$targetPath} 2>&1";
        }
        // 执行命令
        $output = shell_exec($command);
        if (empty($output)) {
            throw new Exception("克隆失败，请检查仓库地址");
        }
        return $output;
    }

    /**
     * 拉取更新
     * @param string $path
     * @throws \Exception
     * @return bool|string|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function pull(string $path)
    {
        // 验证函数是否开启
        self::verifyExec('shell_exec');
        if (!is_dir($path)) {
            throw new Exception('仓库目录不存在');
        }
        // 克隆命令
        $command = "cd {$path} && git pull 2>&1";
        // 执行命令
        $output = shell_exec($command);
        if (empty($output)) {
            throw new Exception("拉取更新失败，请检查仓库地址");
        }
        return $output;
    }

    /**
     * 验证函数是否开启
     * @param string $funName
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function verifyExec(string $funName = 'exec')
    {
        if (!function_exists($funName)) {
            throw new Exception("应用插件操作，未开启【{$funName}】函数");
        }
    }
}
