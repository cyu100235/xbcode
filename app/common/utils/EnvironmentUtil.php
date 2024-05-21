<?php
namespace app\common\utils;
/**
 * 环境检测规则
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class EnvironmentUtil
{
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
            'title'     => '/plugin',
            'dir'       => '/plugin',
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
    ];

    /**
     * 扩展列表
     * @var array
     */
    private static $extraList = [
        [
            'title'   => 'php 8.0',
            'name'    => 'php',
            'min'     => '8.0',
            'max'     => '8.1',
            'type'    => 'version',
            'status'  => false,
            'value'   => 'fail'
        ],
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
    ];

    /**
     * 获取目录权限
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getDirData()
    {
        $data = self::$dirList;
        foreach ($data as $key => $value) {
            $dirPath = base_path() . $value['dir'];
            if (!is_writable($dirPath)) {
                $data[$key]['status'] = false;
                $data[$key]['value']  = '无写入权限';
                continue;
            }
            if (!is_readable($dirPath)) {
                $data[$key]['status'] = false;
                $data[$key]['value']  = '无可读权限';
                continue;
            }
            $data[$key]['status'] = true;
            $data[$key]['value']  = 'Ok';
        }
        return $data;
    }
    
    /**
     * 获取需要验证的开启函数
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getVerifyFun()
    {
        $data = self::$funList;
        foreach ($data as $key => $value) {
            $data[$key]['status'] = function_exists($value['name']) ? true : false;
            $data[$key]['value']  = function_exists($value['name']) ? 'OK' : 'Fail';
        }
        return $data;
    }
    
    /**
     * 获取需要验证的扩展
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getVerifyExtra()
    {
        $data = self::$extraList;
        foreach ($data as $key => $value) {
            switch ($value['type']) {
                case 'extra':
                    $data[$key]['status'] = extension_loaded($value['name']) ? true : false;
                    $data[$key]['value'] = extension_loaded($value['name']) ? 'OK' : 'Fail';
                    break;
                case 'class':
                    $data[$key]['status'] = class_exists($value['name']) ? true : false;
                    $data[$key]['value'] = class_exists($value['name']) ? 'OK' : 'Fail';
                    break;
                case 'function':
                    $data[$key]['status'] = function_exists($value['name']) ? true : false;
                    $data[$key]['value'] = function_exists($value['name']) ? 'OK' : 'Fail';
                    break;
                case 'version':
                    if ($value['name'] === 'php') {
                        $max                  = (bool) version_compare(PHP_VERSION, $value['min'], '>=');
                        $min                  = (bool) version_compare(PHP_VERSION, $value['max'], '<');
                        $data[$key]['status'] = $max && $min;
                        $data[$key]['value']  = $data[$key]['status'] ? 'OK' : "{$value['name']}必须是 >= {$value['min']} < {$value['max']}";
                    }
                    break;
            }
        }
        return $data;
    }
}