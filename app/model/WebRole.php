<?php
namespace app\model;

use xbcode\Model;
use xbcode\providers\MenuProvider;

/**
 * 站点角色模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class WebRole extends Model
{
    // 设置JSON字段转换
    protected $json = ['rule'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    /**
     * 检查是否有权限
     * @param int $adminId
     * @param string $path
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checkAuth(int $roleId, string $path)
    {
        $app     = request()->app;
        $plugins = WebPlugin::getWebAuthPlugin();
        $plugins = array_column($plugins, 'name');
        $plugins = array_merge($plugins, [$app]);
        $rules   = MenuProvider::getWebRoleRules($roleId, $plugins);
        if (in_array($path, $rules)) {
            return true;
        }
        return false;
    }
}
