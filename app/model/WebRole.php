<?php
namespace app\model;

use xbcode\Model;

/**
 * 站点角色模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebRole extends Model
{
    /**
     * 检查是否有权限
     * @param int $adminId
     * @param string $path
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checkAuth(int $adminId, string $path)
    {
        return true;
    }
}
