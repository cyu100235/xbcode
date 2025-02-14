<?php
namespace plugin\xbCode\utils;

use Exception;
use plugin\xbCode\utils\zip\PclZipUtil;
use plugin\xbCode\utils\zip\SystemZipCmdUtil;
use plugin\xbCode\utils\zip\PhpZipArchiveUtil;

/**
 * 使用ZIP管理器
 * @author 贵州猿创科技有限公司
 * @copyright 贵州猿创科技有限公司
 * @email 416716328@qq.com
 */
class ZipUtil
{
    /**
     * 执行打包
     * @param string $zipFilePath 打包文件路径
     * @param string $extractTo 打包目标路径
     * @param array $ignoreFiles 忽略文件
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function build(string $zipFilePath, string $extractTo, array $ignoreFiles = [])
    {
        // 开始执行打包
        if (SystemZipCmdUtil::getZipBuildCmd($zipFilePath, $extractTo)) {
            // 系统级命令打包
            if (!function_exists('proc_open')) {
                throw new Exception('请解除proc_open函数的禁用');
            }
            // 检测是否支持系统命令打包
            if (!SystemZipCmdUtil::getZipBuildCmd($zipFilePath, $extractTo)) {
                throw new Exception('无法使用系统命令进行打包');
            }
            // 开始执行系统打包
            SystemZipCmdUtil::build($zipFilePath, $extractTo, $ignoreFiles);
        } else if (class_exists('ZipArchive')) {
            // 使用ZipArchive扩展打包
            (new PhpZipArchiveUtil)->build($zipFilePath, $extractTo, $ignoreFiles);
        } else {
            // 使用pclzip扩展打包
            (new PclZipUtil)->build($zipFilePath, $extractTo, $ignoreFiles);
        }
    }

    /**
     * 根据路径打包所需文件
     * @param string $zipFilePath
     * @param string $extractTo
     * @param array $files
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function buildFiles(string $zipFilePath, string $extractTo, array $files = [])
    {
        // 开始执行打包
        if (SystemZipCmdUtil::getZipBuildCmd($zipFilePath, $extractTo)) {
            // 系统级命令打包
            if (!function_exists('proc_open')) {
                throw new Exception('请解除proc_open函数的禁用');
            }
            // 检测是否支持系统命令打包
            if (!SystemZipCmdUtil::getZipBuildCmd($zipFilePath, $extractTo)) {
                throw new Exception('无法使用系统命令进行打包');
            }
            // 开始执行系统打包
            SystemZipCmdUtil::buildFiles($zipFilePath, $extractTo, $files);
        } else if (class_exists('ZipArchive')) {
            // 使用内置PHP扩展ZipArchive扩展打包
            (new PhpZipArchiveUtil)->buildFiles($zipFilePath, $extractTo, $files);
        } else {
            // 使用pclzip扩展打包
            (new PclZipUtil)->buildFiles($zipFilePath, $extractTo, $files);
        }
    }

    /**
     * 解压
     * @param string $zipFilePath
     * @param string $extractTo
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function unzip(string $zipFilePath, string $extractTo)
    {
        // 检测目标目录不存在则创建
        if (!is_dir($extractTo)) {
            mkdir($extractTo, 0755, true);
        }
        // 开始执行打包
        if (SystemZipCmdUtil::getUnzipCmd($zipFilePath, $extractTo)) {
            // 系统级命令打包
            if (!function_exists('proc_open')) {
                throw new Exception('请解除proc_open函数的禁用');
            }
            // 检测是否支持系统命令打包
            if (!SystemZipCmdUtil::getZipBuildCmd($zipFilePath, $extractTo)) {
                throw new Exception('无法使用系统命令进行解压');
            }
            // 开始执行系统打包
            SystemZipCmdUtil::unzip($zipFilePath, $extractTo);
        } else if (class_exists('ZipArchive')) {
            // 使用内置PHP扩展ZipArchive扩展解压
            (new PhpZipArchiveUtil)->unzip($zipFilePath, $extractTo);
        } else {
            // 使用pclzip扩展解压
            (new PclZipUtil)->unzip($zipFilePath, $extractTo);
        }
    }
}