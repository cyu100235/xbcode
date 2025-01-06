<?php
namespace xbcode\providers\menus;

use support\Cache;
use app\model\Admin;
use app\model\AdminRole;
use app\model\AdminRule;
use xbcode\providers\MenuProvider;

/**
 * 总后台菜单
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait AdminMenuTrait
{
    /**
     * 获取总管理员菜单数据
     * @param int $uid
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAdminMenus(int $uid)
    {
        $model = Admin::where('id', $uid)->find();
        if (!$model) {
            throw new \Exception('管理员信息错误，请重新登录', 12000);
        }
        $where = [
            ['plugin', '=', ''],
        ];
        // 检测是否系统管理员
        if ($model['is_system'] === '20') {
            // 获取全部菜单数据
            $data = MenuProvider::menuList($where);
        } else {
            // 获取角色权限
            $rules = self::getRoleRules($model['role_id']);
            // 获取菜单数据
            $data = MenuProvider::menuList([
                ['path', 'in', $rules],
                ['plugin', '=', ''],
            ]);
        }
        // 解析数据格式
        $data = MenuProvider::parseMenu($data);
        // 返回数据
        return $data;
    }

    /**
     * 获取总后台角色权限
     * @param int $roleId
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getRoleRules(int $roleId)
    {
        $key = "admin_rule_{$roleId}";
        $data = Cache::get($key);
        if ($data) {
            return $data;
        }
        $data  = AdminRole::where('id', $roleId)->value('rule', [], true);
        $rules = [];
        foreach ($data as $path) {
            // 添加自身
            $rules[] = $path;
            // 查询自身是否有父级规则
            $where  = [
                ['path', '=', $path],
                ['plugin', '=', null],
            ];
            $parent = AdminRule::where($where)->value('pid');
            if ($parent !== 0) {
                // 获取父级规则
                $parentPaths = self::getParentRules((int) $parent);
                if ($parentPaths) {
                    $rules = array_merge($rules, $parentPaths);
                }
            }
        }
        // 去除重复
        $rules = array_unique($rules);
        // 缓存数据
        Cache::set($key, $rules, 300);
        // 返回数据
        return $rules;
    }
}