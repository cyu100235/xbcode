<?php
namespace plugin\xbCode\app\install\utils;

/**
 * 环境检测规则
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class EnvironmentUtil
{
    /**
     * 获取目录权限
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getDirData()
    {
        $data = include dirname(__DIR__) . '/config/dir.php';
        foreach ($data as $key => $value) {
            $dirPath = base_path() . $value['dir'];
            $data[$key]['status_text']  = '读写权限';
            $data[$key]['status'] = false;
            // 是否可写
            if (!is_writable($dirPath)) {
                $data[$key]['status'] = false;
                $data[$key]['value']  = '无写入权限';
                continue;
            }
            // 是否可读
            if (!is_readable($dirPath)) {
                $data[$key]['status'] = false;
                $data[$key]['value']  = '无读取权限';
                continue;
            }
            $data[$key]['status'] = true;
            $data[$key]['value']  = '验证通过';
        }
        return $data;
    }
    
    /**
     * 获取需要验证的开启函数
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getVerifyFun()
    {
        $data = include dirname(__DIR__) . '/config/fun.php';
        foreach ($data as $key => $value) {
            $data[$key]['status'] = function_exists($value['name']) ? true : false;
            $data[$key]['status_text'] = $data[$key]['status'] ?'已解除禁用' : '未解除禁用';
            $data[$key]['value']  = $data[$key]['status'] ? '验证通过' : '验证失败';
        }
        return $data;
    }
    
    /**
     * 获取需要验证的扩展
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getVerifyExtra()
    {
        $data = include dirname(__DIR__) . '/config/extra.php';
        foreach ($data as $key => $value) {
            $data[$key]['status'] = false;
            $data[$key]['status_text'] = '安装扩展';
            $data[$key]['value'] = '验证失败';
            switch ($value['type']) {
                case 'extra':
                    $data[$key]['status'] = extension_loaded($value['name']) ? true : false;
                    $data[$key]['value'] = $data[$key]['status'] ? '验证通过' : '验证失败';
                    break;
                case 'class':
                    $data[$key]['status'] = class_exists($value['name']) ? true : false;
                    $data[$key]['value'] = $data[$key]['status'] ? '验证通过' : '验证失败';
                    break;
                case 'function':
                    $data[$key]['status'] = function_exists($value['name']) ? true : false;
                    $data[$key]['value'] = $data[$key]['status'] ? '验证通过' : '验证失败';
                    break;
                case 'version':
                    if ($value['name'] === 'php') {
                        $max                  = (bool) version_compare(PHP_VERSION, $value['min'], '>=');
                        $min                  = (bool) version_compare(PHP_VERSION, $value['max'], '<');
                        $data[$key]['status'] = $max && $min;
                        $data[$key]['value']  = $data[$key]['status'] ? '验证通过' : "{$value['name']}必须是 >= {$value['min']} < {$value['max']}";
                    }
                    break;
            }
        }
        return $data;
    }

    /**
     * 获取环境检测数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getEnvironment()
    {
        $data = include dirname(__DIR__) . '/config/environment.php';
        foreach ($data as $key => $value) {
            switch ($value['type']) {
                case 'mysql':
                    $data[$key]['status'] = true;
                    $data[$key]['status_text'] = $value['value'];
                    $data[$key]['value'] = '--';
                    break;
                case 'disk':
                    // 设置最低要求
                    $data[$key]['status_text'] = "最低：{$value['value']}MB";
                    // MB换算成字节
                    $value['value'] = $value['value'] * 1024 * 1024;
                    // 获取项目路径
                    $projectPath = base_path();
                    $diskFreeSpace = disk_free_space($projectPath);
                    $diskSize = get_size($diskFreeSpace);
                    $data[$key]['status'] = $diskFreeSpace >= $value['value'] ? true : false;
                    $data[$key]['value'] = $diskSize;
                    break;
                case 'system':
                    // 首字母转大写
                    $systemName = ucfirst(strtolower(PHP_OS));
                    if (is_array($value['value'])) {
                        $data[$key]['status'] = in_array($systemName, $value['value']) ? true : false;
                    }
                    $data[$key]['status_text'] = "要求系统：" . implode('、', $value['value']);
                    $data[$key]['value'] = $systemName;
                    break;
                case 'version':
                    if ($value['name'] === 'php') {
                        $max                  = (bool) version_compare(PHP_VERSION, $value['min'], '>=');
                        $min                  = (bool) version_compare(PHP_VERSION, $value['max'], '<');
                        $data[$key]['status'] = $max && $min;
                    }
                    $data[$key]['status_text'] = "版本要求：{$value['min']} - {$value['max']}";
                    $data[$key]['value'] = PHP_VERSION;
                    break;
            }
        }
        return $data;
    }

    /**
     * 获取环境检测数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get()
    {
        $data = [
            [
                'title' => '环境要求',
                'type' => 'environment',
                'children' => self::getEnvironment()
            ],
            [
                'title' => '扩展检测',
                'type' => 'extra',
                'children' => self::getVerifyExtra()
            ],
            [
                'title' => '目录权限检测',
                'type' => 'dir',
                'children' => self::getDirData()
            ],
            [
                'title' => '函数检测',
                'type' => 'function',
                'children' => self::getVerifyFun()
            ],
        ];
        return $data;
    }
}