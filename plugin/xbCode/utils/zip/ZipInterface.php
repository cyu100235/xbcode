<?php
namespace plugin\xbCode\utils\zip;

/**
 * 压缩包管理器接口
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
interface ZipInterface
{
    /**
     * 打包某目录下所有文件
     * @param string $zipFilePath 打包至目标压缩包
     * @param string $extractTo 打包目标路径
     * @param array $ignoreFiles 需要忽略的绝对目录路径或者文件（可选）
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function build(string $zipFilePath, string $extractTo, array $ignoreFiles = []);

    /**
     * 打包所需文件
     * @param string $zipFilePath 压缩包路径
     * @param string $extractTo 打包目标路径
     * @param array $files 需要打包的文件
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function buildFiles(string $zipFilePath,string $extractTo, array $files);

    /**
     * 解压目标压缩包至目录
     * @param string $zipFilePath 压缩包路径
     * @param string $tarGetPath 解压至目标路径
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function unzip(string $zipFilePath, string $extractTo);
}