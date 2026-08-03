<?php
/**
 * 积木云渲染器
 * @package  XbCode.0
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

/**
 * 管理员权限接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class AdminAuthApi
{
    /**
     * 获取实例
     * @return AdminAuthApi
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function make()
    {
        $class = new static;
        return $class;
    }

    /**
     * 检测是否有操作权限
     * @param int $roleId
     * @param string $url
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function Authentication(int $roleId, string $url)
    {
        return true;
    }
}