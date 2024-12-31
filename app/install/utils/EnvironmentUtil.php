<?php
namespace app\install\utils;

/**
 * 环境检测规则
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class EnvironmentUtil
{
    /**
     * 环境参数
     * @var array
     */
    private static $environment = [
        [
            'title' => '操作系统',
            'type'    => 'system',
            'status' => false,
            'value' => [
                'Linux',
                'Windows'
            ],
        ],
        [
            'title' => '磁盘空间',
            'type'    => 'disk',
            'status' => false,
            'value' => 300,
        ],
        [
            'title'   => 'php版本',
            'name'    => 'php',
            'min'     => '8.0',
            'max'     => '8.1',
            'type'    => 'version',
            'status'  => false,
            'value'   => '最低PHP8.0以上版本'
        ],
        [
            'title'   => 'mysql版本',
            'name'    => 'mysql',
            'type'    => 'mysql',
            'status'  => false,
            'value'   => '建议使用5.7版本'
        ],
    ];

    /**
     * 目录列表
     * @var array
     */
    private static $dirList = [
        [
            'title'     => '/config',
            'dir'       => '/config',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => '/runtime',
            'dir'       => '/runtime',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => '/public',
            'dir'       => '/public',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => '/public/uploads',
            'dir'       => '/public/uploads',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => '/settings',
            'dir'       => '/settings',
            'status'    => false,
            'value'     => 'fail'
        ],
    ];

    /**
     * 函数列表
     * @var array
     */
    private static $funList = [
        [
            'title'     => 'putenv',
            'name'      => 'putenv',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'shell_exec',
            'name'      => 'shell_exec',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'stream_socket_server',
            'name'      => 'stream_socket_server',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'stream_socket_client',
            'name'      => 'stream_socket_client',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'pcntl_signal_dispatch',
            'name'      => 'pcntl_signal_dispatch',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'pcntl_signal',
            'name'      => 'pcntl_signal',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'pcntl_alarm',
            'name'      => 'pcntl_alarm',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'pcntl_fork',
            'name'      => 'pcntl_fork',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_getuid',
            'name'      => 'posix_getuid',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_getpwuid',
            'name'      => 'posix_getpwuid',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_kill',
            'name'      => 'posix_kill',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_setsid',
            'name'      => 'posix_setsid',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_getpid',
            'name'      => 'posix_getpid',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_getpwnam',
            'name'      => 'posix_getpwnam',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_getgrnam',
            'name'      => 'posix_getgrnam',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_getgid',
            'name'      => 'posix_getgid',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_setgid',
            'name'      => 'posix_setgid',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_initgroups',
            'name'      => 'posix_initgroups',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_setuid',
            'name'      => 'posix_setuid',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'posix_isatty',
            'name'      => 'posix_isatty',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'proc_open',
            'name'      => 'proc_open',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'proc_get_status',
            'name'      => 'proc_get_status',
            'status'    => false,
            'value'     => 'fail'
        ],
        [
            'title'     => 'proc_close',
            'name'      => 'proc_close',
            'status'    => false,
            'value'     => 'fail'
        ],
    ];

    /**
     * 扩展列表
     * @var array
     */
    private static $extraList = [
        [
            'title'  => 'fileinfo',
            'name'   => 'fileinfo',
            'type'   => 'extra',
            'status' => false,
            'value'  => 'fail'
        ],
        [
            'title'  => 'swoole',
            'name'   => 'swoole',
            'type'   => 'extra',
            'status' => false,
            'value'  => 'fail'
        ],
        [
            'title'  => 'redis',
            'name'   => 'Redis',
            'type'   => 'class',
            'status' => false,
            'value'  => 'fail'
        ],
        [
            'title'  => 'curl',
            'name'   => 'curl',
            'type'   => 'extra',
            'status' => false,
            'value'  => 'fail'
        ],
        [
            'title'  => 'gd',
            'name'   => 'gd',
            'type'   => 'extra',
            'status' => false,
            'value'  => 'fail'
        ],
        [
            'title'  => 'exif',
            'name'   => 'exif',
            'type'   => 'extra',
            'status' => false,
            'value'  => 'fail'
        ],
    ];

    /**
     * 获取目录权限
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getDirData()
    {
        $data = self::$dirList;
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
        $data = self::$funList;
        foreach ($data as $key => $value) {
            $data[$key]['status'] = function_exists($value['name']) ? true : false;
            $data[$key]['status_text'] = $data[$key]['status'] ?'解除函数禁用' : '未解除禁用';
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
        $data = self::$extraList;
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
        $data = self::$environment;
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