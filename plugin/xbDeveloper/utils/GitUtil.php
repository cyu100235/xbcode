<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
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
     * @throws Exception
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
     * 创建插件子模块
     * @param string $url 仓库地址
     * @param string $name 插件标识
     * @throws Exception
     * @return bool|string|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function subModules(string $url,string $name)
    {
        // 验证函数是否开启
        self::verifyExec('shell_exec');
        // 获取仓库模块列表
        $modules = static::getModules();
        if (in_array($name, $modules)) {
            throw new Exception('该插件标识已经存在');
        }
        // 目标地址
        $targetPath = "plugin/{$name}";
        // 转换路径
        $url = escapeshellcmd($url);
        // 克隆命令
        $command = "git submodule add -f {$url} {$targetPath} 2>&1";
        if (!empty($branch)) {
            $command = "git submodule add -f {$branch} {$url} {$targetPath} 2>&1";
        }
        // 执行命令
        $output = shell_exec($command);
        if (empty($output)) {
            throw new Exception("克隆子模块失败，请检查仓库地址");
        }
        return $output;
    }

    /**
     * 克隆递归克隆主项目和子模块仓库
     * @param string $url 仓库地址
     * @param string $targetPath 克隆路径
     * @param string $branch 分支名称
     * @throws Exception
     * @return bool|string|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function cloneModules(string $url, string $targetPath, string $branch = '')
    {
        // 验证函数是否开启
        self::verifyExec('shell_exec');
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0777, true);
        }
        // 转换路径
        $url = escapeshellcmd($url);
        // 克隆命令
        $command = "git clone --recurse-submodules {$url} {$targetPath} 2>&1";
        if (!empty($branch)) {
            $command = "git clone --recurse-submodules -b {$branch} {$url} {$targetPath} 2>&1";
        }
        // 执行命令
        $output = shell_exec($command);
        if (empty($output)) {
            throw new Exception("克隆失败，请检查仓库地址");
        }
        return $output;
    }
    
    /**
     * 判断目标目录是否git仓库或子模块
     * @param string $dirPath
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function hasGitRepository(string $dirPath): bool
    {
        if (!is_dir($dirPath)) {
            return false;
        }
        $name = basename($dirPath);
        $modulePath = base_path("/.git/modules/plugin/{$name}");
        if (is_dir($dirPath . '/.git') || is_dir($modulePath)) {
            return true;
        }
        return false;
    }
    
    /**
     * 推送代码至远程仓库
     * @param string $path 仓库路径
     * @param string $commit 提交信息
     * @throws Exception
     * @return bool|string|null
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function push(string $path, string $commit)
    {
        self::verifyExec('shell_exec');
        if (!is_dir($path)) {
            throw new Exception('仓库目录不存在');
        }
        if (!is_writable($path)) {
            throw new Exception('仓库目录无写权限');
        }
        if (empty($commit)) {
            throw new Exception('提交信息不能为空');
        }
        // 判断是否有文件变动
        $command = "cd {$path} && git status 2>&1";
        $output = (string) shell_exec($command);
        if (empty($output)) {
            throw new Exception('判断文件变动失败，请检查仓库地址');
        }
        if (str_contains($output, 'nothing to commit')) {
            throw new Exception('没有可提交的变更，请先修改文件');
        }
        // 转义提交信息，防止命令注入
        $commit = escapeshellarg($commit);
        // 添加所有文件
        $command = "cd {$path} && git add . 2>&1";
        $output = (string) shell_exec($command);
        $outputLower = strtolower($output);
        if (str_contains($outputLower, 'error') || str_contains($outputLower, 'fatal')) {
            throw new Exception('添加文件失败：' . trim($output));
        }
        // 添加提交信息
        $command = "cd {$path} && git commit -m {$commit} 2>&1";
        $output = (string) shell_exec($command);
        $outputLower = strtolower($output);
        if (empty($output)) {
            throw new Exception('添加提交信息失败，请检查仓库地址');
        }
        if (str_contains($outputLower, 'error') || str_contains($outputLower, 'fatal')) {
            throw new Exception('添加提交信息失败：' . trim($output));
        }
        if (str_contains($outputLower, 'nothing to commit')) {
            throw new Exception('没有可提交的变更，请先修改文件');
        }
        // 提交推送仓库
        $command = "cd {$path} && git push 2>&1";
        $output = shell_exec($command);
        if (empty($output)) {
            throw new Exception('推送仓库失败，请检查仓库地址');
        }
        // 判断推送是否成功：输出中包含 error 或 fatal 则视为失败
        $outputLower = strtolower($output);
        if (str_contains($outputLower, 'error') || str_contains($outputLower, 'fatal')) {
            throw new Exception('推送失败：' . trim($output));
        }
        // 判断仓库是否最新
        if (str_contains($outputLower, 'up-to-date')) {
            throw new Exception('仓库已最新，无需推送');
        }
        return $output;
    }

    /**
     * 拉取更新
     * @param string $path
     * @throws Exception
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
     * 获取最近提交记录
     * @param string $dirPath
     * @param int $limit
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getCommitList(string $dirPath, int $limit = 10)
    {
        self::verifyExec('shell_exec');
        if (!is_dir($dirPath)) {
            throw new Exception('仓库目录不存在');
        }
        // 获取提交信息命令（日期格式：yyyy-MM-dd HH:mm:ss，正序排列）
        $command = "cd {$dirPath} && git log -n {$limit} --reverse --pretty=format:'{\"commit\":\"%h\",\"author\":\"%an\",\"date\":\"%ad\",\"subject\":\"%s\"}' --date=format:'%Y-%m-%d %H:%M:%S'";
        // 执行命令
        $output = shell_exec($command);
        if (empty($output)) {
            throw new Exception("获取提交信息失败，请检查仓库地址");
        }
        // 将输出按行分割并转换为数组
        $lines = array_filter(explode("\n", $output), fn($line) => !empty(trim($line)));
        $data = [];
        foreach ($lines as $line) {
            $json = json_decode($line, true);
            if ($json !== null) {
                $data[] = $json;
            }
        }
        return $data;
    }

    /**
     * 获取子仓库模块列表
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getModules()
    {
        $modulePath = base_path('/.git/modules/plugin');
        if (!is_dir($modulePath)) {
            return [];
        }
        $modules = glob($modulePath . '/*', GLOB_ONLYDIR);
        $modules = array_map(function ($module) {
            $module = str_replace("\\", '/', $module);
            return basename($module);
        }, $modules);
        return $modules;
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
