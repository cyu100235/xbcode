<?php
namespace app\utils\zip;

use Exception;
use ZipArchive;

/**
 * 原生PHP-ZipArchive打包管理器
 * @author 贵州小白基地网络科技有限公司
 * @copyright (c) 贵州小白基地网络科技有限公司
 */
class PhpZipArchiveUtil
{
    /**
     * 是否支持ZipArchive扩展
     * @var bool
     */
    protected $hasZipArchive = false;

    /**
     * 构造函数
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function __construct()
    {
        if (!class_exists("ZipArchive")) {
            throw new Exception('请给php安装zip模块');
        }
    }

    /**
     * 打包某目录下所有文件
     * TODO：该方法打包出来会多出来一个_，暂时不知道原因
     * @param string $zipFilePath 打包至目标压缩包
     * @param string $extractTo 打包目标路径
     * @param array $ignoreFiles 需要忽略的绝对目录路径或者文件（可选）
     * @return void
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function build(string $zipFilePath, string $extractTo, array $ignoreFiles = [])
    {
        // 检测目标目录是否有权限
        if (!is_writable($extractTo)) {
            throw new Exception('打包目录无权限');
        }
        $zip = new ZipArchive;
        $openStatus = $zip->open($zipFilePath, ZipArchive::CREATE);
        if ($openStatus !== true) {
            throw new Exception('打包失败');
        }
        // 执行递归打包
        self::addFileToZip($zip, $extractTo, '/', $ignoreFiles);
        // 关闭资源
        $zip->close();
    }

    /**
     * 打包所需文件
     * @param string $zipFilePath
     * @param array $files
     * @return void
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function buildFiles(string $zipFilePath, string $extractTo, array $files)
    {
        // 检测目标目录是否有权限
        if (!is_writable($extractTo)) {
            throw new Exception('解压目录无权限');
        }
        $zip = new ZipArchive;
        $openStatus = $zip->open($zipFilePath, ZipArchive::CREATE);
        if ($openStatus !== true) {
            throw new Exception('打包失败');
        }
        // 执行递归打包
        foreach ($files as $file) {
            $filePath = "{$extractTo}/{$file}";
            if (is_file($filePath) && file_exists($filePath)) {
                $zip->addFile($filePath, $file);
            } else if (is_dir($filePath)) {
                $this->addFileToZip($zip, $filePath, "{$file}/");
            }
        }
        // 关闭资源
        $zip->close();
    }

    /**
     * 扫描目录并添加文件至压缩包
     * @param \ZipArchive $zip
     * @param string $sourcePath
     * @param string $zipPath
     * @param array $ignoreFiles
     * @return void
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    private function addFileToZip(ZipArchive $zip, string $extractTo, string $zipPath = '/', array $ignoreFiles = [], $local_parent_path = null)
    {
        $files = scandir($extractTo);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                if ($local_parent_path) {
                    $local_path = trim("{$local_parent_path}/{$file}", '/'); // 相对zip资源内的路径
                } else {
                    $local_path = trim("$file", '/'); // 相对zip资源内的路径
                }
                $path = $extractTo . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    if (!in_array(rtrim($local_path, '/'), $ignoreFiles)) {
                        $zip->addEmptyDir($zipPath . $file);
                        $this->addFileToZip($zip, $path, $zipPath . $file . DIRECTORY_SEPARATOR, $ignoreFiles, $local_path);
                    }
                } else if (is_file($path) && file_exists($path)) {
                    if (!in_array($path, $ignoreFiles)) {
                        $zip->addFile($path, $zipPath . $file);
                    }
                }
            }
        }
    }

    /**
     * 解压目标压缩包至目录
     * @param string $zipFilePath 压缩包路径
     * @param string $tarGetPath 解压至目标路径
     * @return void
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function unzip(string $zipFilePath, string $tarGetPath)
    {
        // 检测压缩包是否存在
        if (!file_exists($zipFilePath)) {
            throw new Exception('压缩包不存在' . $zipFilePath);
        }
        // 检测目录不存在则创建
        if (!is_dir($tarGetPath)) {
            mkdir($tarGetPath, 0755, true);
        }
        $zip = new ZipArchive;
        $errCode = $zip->open($zipFilePath, ZipArchive::CHECKCONS);
        if ($errCode !== true) {
            throw new Exception('解压失败，错误吗:' . $errCode);
        }
        // 解压至目标目录
        $zip->extractTo($tarGetPath);
        // 关闭资源
        $zip->close();
    }
}