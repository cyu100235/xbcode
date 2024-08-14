<?php
namespace app\common\utils;

use Exception;

/**
 * 目录工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DirUtil
{
    /**
     * 获取目录下的PHP文件数据
     * @param string $dirPath 目录路径
     * @param array $exclude 排除文件
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getDirFileData(string $dirPath, array $exclude = [])
    {
        // 检测目录是否以斜杠结尾
        if (substr($dirPath, -1) != '/') {
            $dirPath .= '/';
        }
        // 扫描配置文件
        $dirs = glob($dirPath . "*.php");
        // 排除文件
        $dirs = array_filter($dirs, function ($value) use ($dirPath, $exclude) {
            $path = str_replace($dirPath, '', $value);
            if (in_array($path, $exclude)) {
                return false;
            }
            return true;
        });
        $data = [];
        foreach ($dirs as $value) {
            $temp = include $value;
            if (empty($temp)) {
                continue;
            }
            $path        = str_replace($dirPath, '', $value);
            $name        = str_replace(".php", '', $path);
            $data[$name] = $temp;
        }
        return $data;
    }

    /**
     * 获取PHP文件内容
     * @param string $file
     * @param mixed $default
     * @throws \Exception
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getFileContent(string $file, mixed $default = null)
    {
        if (!file_exists($file)) {
            throw new Exception('文件不存在');
        }
        $data = include $file;
        if (empty($data)) {
            throw new Exception('文件内容为空');
        }
        if (!is_array($data)) {
            throw new Exception('文件格式错误');
        }
        return $data;
    }
}
