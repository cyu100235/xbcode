<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\api;

/**
 * SSH密钥接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IdRsaApi
{
    /**
     * 获取实例
     * @return IdRsaApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 获取本地SSH公钥内容
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function getIdRsaContent()
    {
        // 尝试获取用户家目录
        $homeDir = '';

        // 方式1: 从环境变量获取
        if (!empty($_SERVER['HOME'])) {
            $homeDir = $_SERVER['HOME'];
        }
        // 方式2: 使用POSIX函数获取（需要posix扩展）
        elseif (function_exists('posix_getuid') && function_exists('posix_getpwuid')) {
            $userInfo = posix_getpwuid(posix_getuid());
            if (is_array($userInfo) && isset($userInfo['dir'])) {
                $homeDir = $userInfo['dir'];
            }
        }

        // 如果无法获取家目录，返回提示
        if (empty($homeDir)) {
            return '无法获取系统用户家目录，请联系管理员配置SSH公钥';
        }

        // 构造公钥文件路径
        $idRsaPath = $homeDir . '/.ssh/id_rsa.pub';

        // 检查文件是否存在并读取
        if (file_exists($idRsaPath) && is_readable($idRsaPath)) {
            $content = file_get_contents($idRsaPath);
            $content = (string) $content;
            return $content !== false ? $content : '读取SSH公钥文件失败';
        }

        return '未找到SSH公钥文件 (' . $idRsaPath . ')';
    }
}