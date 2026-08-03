<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @version  1.0.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\api;

use Exception;
use plugin\xbCode\api\Menus;
use plugin\xbCode\api\Mysql;
use plugin\xbCode\api\DebugApi;
use plugin\xbCode\utils\DirUtil;
use plugin\xbCode\utils\ZipUtil;
use plugin\xbCode\api\PluginsApi;

/**
 * 开发辅助接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class DevelopmentApi
{
    /**
     * 创建实例
     * @return static
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 获取插件列表
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getList()
    {
        $pluginAPI = PluginsApi::make();
        // 刷新缓存
        $pluginAPI->getCache(true);
        $data = $pluginAPI->getList();
        $data = array_map(function ($value) {
            // 检测是否可以打包
            $value['can_package'] = $this->canPackage($value['name']) ? '20' : '10';
            // 是否可以推送仓库
            $value['can_push'] = DepositoryApi::make($value['name'])->isGitRepo() ? '20' : '10';
            return $value;
        }, $data);
        return $data;
    }

    /**
     * 构建补丁包
     * @param string $name 插件标识
     * @param string $version 版本编号
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function buildePatchPackage(string $name, string $version)
    {
        // 获取插件git文件变化列表
        $gitChange = $this->getPackageFilesChange($name);
        // 获取插件SQL表结构变动列表
        $sqlChange = TableStructureApi::make()->getPackageSqlChange($name);
        if (empty($gitChange) && empty($sqlChange)) {
            throw new Exception('无法构建补丁包，文件和表结构无变动');
        }
        // 版本目录
        $versionDir = $this->getVersionPatchFile($name, $version);
        $versionDir = dirname($versionDir);
        if (!is_dir($versionDir)) {
            mkdir($versionDir, 0777, true);
        }
        // 复制文件列表
        $this->copyPackageFiles($name, $version, $gitChange);
        // 执行版本文件代码为补丁包
        $this->executePackageFiles($name, $version);
        // 写入SQL文件
        $this->writePackageSql($name, $version, $sqlChange);
        // 合并补丁SQL为一个文件
        $this->mergePackageSql($name, $version);
        // 清理插件在 runtime 下的临时目录（zip 在 plugin-version 根目录，不在此路径）
        $this->cleanupPluginVersionWorkDir($name);
    }

    /**
     * 复制文件列表
     * @param string $name 插件标识
     * @param string $version 版本编号
     * @param array $files 文件列表
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function copyPackageFiles(string $name, string $version, array $files)
    {
        $targetDirPath = $this->getVersionCodeDirPath($name, $version);
        foreach ($files as $file) {
            $filePath = base_path("plugin/{$name}/{$file}");
            if (!file_exists($filePath)) {
                continue;
            }
            $target = $targetDirPath . '/' . $file;
            // 目录不能用于 copy()，需递归复制（如 Git 路径为目录、或路径为指向目录的符号链接）
            if (is_dir($filePath)) {
                DirUtil::copyDir($filePath, $target);
                continue;
            }
            if (!is_file($filePath)) {
                continue;
            }
            if (!is_dir(dirname($target))) {
                mkdir(dirname($target), 0777, true);
            }
            copy($filePath, $target);
        }
    }

    /**
     * 执行补丁代码打包
     * @param string $name
     * @param string $version
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function executePackageFiles(string $name, string $version)
    {
        $dirPath = $this->getVersionCodeDirPath($name, $version);
        $zipPath = $this->getVersionPatchFile($name, $version);
        // 检测目录是否存在
        if (!is_dir($dirPath)) {
            return;
        }
        ZipUtil::build($zipPath, $dirPath);
        // 删除版本临时目录
        DirUtil::delDir($dirPath);
    }

    /**
     * 删除插件补丁构建时使用的 runtime 临时目录（如 runtime/plugin-version/{name}）
     * @param string $name 插件标识
     * @return void
     */
    private function cleanupPluginVersionWorkDir(string $name): void
    {
        $path = base_path("runtime/plugin-version/{$name}");
        if (is_dir($path)) {
            DirUtil::delDir($path);
        }
    }

    /**
     * 写入补丁包表结构
     * @param string $name
     * @param string $version
     * @param string $sql
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function writePackageSql(string $name, string $version, string $sql)
    {
        if (empty($sql)) {
            return;
        }
        $sqlPath = $this->getVersionPatchSql($name, $version);
        if (!is_dir(dirname($sqlPath))) {
            mkdir(dirname($sqlPath), 0777, true);
        }
        file_put_contents($sqlPath, $sql);
    }

    /**
     * 合并补丁包表结构
     * @param string $name
     * @param string $version
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function mergePackageSql(string $name, string $version)
    {
        $sqlPath = $this->getVersionPatchSql($name, $version);
        if (!is_dir(dirname($sqlPath))) {
            mkdir(dirname($sqlPath), 0777, true);
        }
        $files = glob(base_path("plugin/{$name}/update/*.sql"));
        $sql = '';
        foreach ($files as $file) {
            if (basename($file) === 'update.sql') {
                continue;
            }
            $sql .= "\n";
            $sql .= file_get_contents($file);
        }
        $sqlPath = base_path("plugin/{$name}/update/update.sql");
        file_put_contents($sqlPath, '');
        file_put_contents($sqlPath, $sql);
    }

    /**
     * 获取插件文件变化列表
     * @param string $name
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getPackageFilesChange(string $name)
    {
        $data = $this->getPackageGitChange($name);
        $data = array_filter($data, function ($value) {
            return $value['type'] !== 'deleted';
        });
        $data = array_map(function ($value) {
            return $value['file'];
        }, $data);
        $data = array_values($data);
        return $data;
    }

    /**
     * 获取临时版本代码目录地址
     * @param string $name 插件标识
     * @param string $version 版本编号
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getVersionCodeDirPath(string $name, string $version)
    {
        $path = base_path("runtime/plugin-version/{$name}/{$version}");
        return $path;
    }

    /**
     * 获取插件版本补丁文件
     * @param string $name 插件标识
     * @param string $version 版本编号
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getVersionPatchFile(string $name, string $version)
    {
        $filePath = base_path("runtime/plugin-version/{$name}-{$version}.zip");
        return $filePath;
    }

    /**
     * 获取插件版本补丁SQL文件
     * @param string $name
     * @param string $version
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function getVersionPatchSql(string $name, string $version)
    {
        $sqlPath = base_path("plugin/{$name}/update/{$version}.sql");
        return $sqlPath;
    }

    /**
     * 获取插件git文件变化列表
     * @param string $name 插件标识
     * @return array
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function getPackageGitChange(string $name)
    {
        $pluginDir = base_path() . "/plugin/{$name}";
        // 检测插件目录是否存在
        if (!is_dir($pluginDir)) {
            throw new Exception("插件不存在：{$pluginDir}");
        }
        // 判断是否为独立 git 仓库
        if (!$this->canPackage($name)) {
            throw new Exception("插件 {$name} 非git仓库，不允许打包");
        }
        // 使用 git status --porcelain 获取变化文件列表
        // --porcelain 输出格式：XY filename，X=暂存区状态，Y=工作区状态
        $command = "git -C " . escapeshellarg($pluginDir) . " status --porcelain 2>&1";
        exec($command, $output, $exitCode);
        if ($exitCode !== 0) {
            $errorMsg = implode("\n", $output);
            throw new Exception("执行 Git 命令失败：{$errorMsg}");
        }
        // 状态码映射
        $statusMap = [
            'A' => 'added',    // 新增
            'M' => 'modified', // 修改
            'D' => 'deleted',  // 删除
            'R' => 'renamed',  // 重命名
            'C' => 'copied',   // 复制
            'U' => 'conflict', // 冲突
            '?' => 'untracked', // 未跟踪（新增未暂存）
            '!' => 'ignored',  // 已忽略
        ];
        $changes = [];
        foreach ($output as $line) {
            if (empty(trim($line))) {
                continue;
            }
            // 前两个字符为状态码，第三个字符为空格，之后为文件路径
            $indexStatus = substr($line, 0, 1); // 暂存区状态
            $workStatus = substr($line, 1, 1); // 工作区状态
            $filePath = trim(substr($line, 2));
            // 去除文件路径中的引号（git 对含特殊字符的路径会加引号）
            $filePath = trim($filePath, '"');
            // 忽略 update 目录下的文件
            if (strpos($filePath, 'update/') === 0 || strpos($filePath, 'update\\') === 0) {
                continue;
            }
            // 重命名格式：oldName -> newName
            if (($indexStatus === 'R' || $workStatus === 'R') && strpos($filePath, ' -> ') !== false) {
                [$oldPath, $newPath] = explode(' -> ', $filePath, 2);
                $changes[] = [
                    'file' => trim($newPath),
                    'old_file' => trim($oldPath),
                    'type' => 'renamed',
                    'type_label' => '重命名',
                    'index_status' => $indexStatus,
                    'work_status' => $workStatus,
                ];
                continue;
            }
            // 确定变化类型（优先取暂存区状态，其次取工作区状态）
            $statusCode = ($indexStatus !== ' ' && $indexStatus !== '?') ? $indexStatus : $workStatus;
            $type = $statusMap[$statusCode] ?? 'unknown';
            $typeLabels = [
                'added' => '新增',
                'modified' => '修改',
                'deleted' => '删除',
                'renamed' => '重命名',
                'copied' => '复制',
                'conflict' => '冲突',
                'untracked' => '未跟踪',
                'ignored' => '已忽略',
                'unknown' => '未知',
            ];
            $changes[] = [
                'file' => $filePath,
                'old_file' => null,
                'type' => $type,
                'type_label' => $typeLabels[$type] ?? '未知',
                'index_status' => $indexStatus,
                'work_status' => $workStatus,
            ];
        }
        return $changes;
    }

    /**
     * 检测插件是否允许打包
     * @param string $name
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function canPackage(string $name)
    {
        // 子模块地址
        $submodulePath = base_path() . "/.git/modules/plugin/{$name}";
        // 检测插件目录下是否存在.git目录
        if (is_dir($submodulePath)) {
            return true;
        }
        return false;
    }

    /**
     * 创建插件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function create(array $data)
    {
        // 数据验证
        PluginsCreate::validate($data);
        // 是否调试模式
        $debug = DebugApi::status();
        // 创建插件
        PluginsCreate::create($data, $debug);
    }

    /**
     * 执行安装文件SQL
     * @param string $name 插件标识
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function sql(string $name)
    {
        if (!$name) {
            throw new Exception('插件标识参数错误');
        }
        // 获取SQL文件
        $file = base_path() . "/plugin/{$name}/install.sql";
        if (!file_exists($file)) {
            throw new Exception('插件SQL文件不存在');
        }
        // 执行SQL脚本文件
        Mysql::importSql($file, ['xb_', '__PREFIX__']);
    }

    /**
     * 导出插件
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function export(string $name, string $method)
    {
        if (empty($method)) {
            throw new Exception('执行导出参数错误');
        }
        if (empty($name)) {
            throw new Exception('插件标识参数错误');
        }
        if (!class_exists(PluginsExport::class)) {
            throw new Exception('插件导出类不存在');
        }
        // 首字母转大写
        $method = ucfirst($method);
        // 拼接方法名
        $method = "export{$method}";
        $class = new PluginsExport;
        if (!method_exists($class, $method)) {
            throw new Exception('插件导出方法不存在');
        }
        // 执行导出
        call_user_func([$class, $method], $name);
    }

    /**
     * 导出插件菜单至插件配置
     * @param string $name 插件标识
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function menus(string $name)
    {

        if (!DebugApi::status()) {
            throw new Exception('请先开启开发调试模式');
        }
        // 获取菜单文件
        $file = base_path() . "/plugin/{$name}/config/menu.php";
        if (!file_exists($file)) {
            throw new Exception('插件菜单文件不存在');
        }
        // 获取菜单数据
        $data = include $file;
        // 检测菜单数据
        if (empty($data)) {
            throw new Exception('插件菜单数据为空');
        }
        // 处理菜单数据
        $data = $this->checkMenuData($data);
        // 开始安装更新菜单
        Menus::install($data, $name);
    }

    /**
     * 克隆插件仓库
     * @param array $data 表单数据
     * - `url` 仓库ssh地址
     * - `title` 插件名称
     * - `name` 插件标识
     * - `desc` 插件描述
     * - `author` 作者名称
     * @throws Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function clone(array $data)
    {
        if (!DebugApi::status()) {
            throw new Exception('请先开启开发调试模式');
        }
        if (empty($data)) {
            throw new Exception('插件数据参数错误');
        }
        $debug = DebugApi::status();
        PluginsCreate::clone($data, $debug);
    }

    /**
     * 定制处理菜单
     * @param array $data
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function checkMenuData(array $data)
    {
        foreach ($data as &$item) {
            $item['state'] = '20';
            if (!empty($item['children'])) {
                $item['children'] = $this->checkMenuData($item['children']);
            }
        }
        return $data;
    }
}