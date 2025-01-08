<?php
namespace xbcode\providers\menus;

use support\Cache;
use app\model\WebRole;
use app\model\WebAdmin;
use app\model\AdminRule;
use xbcode\providers\MenuProvider;

/**
 * 站点菜单
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait WebMenuTrait
{
    /**
     * 获取站点菜单数据
     * @param int $uid
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getWebMenus(int $uid, array $plugin = [])
    {
        $model = WebAdmin::where('id', $uid)->find();
        if (!$model) {
            throw new \Exception('站点管理员信错误，请重新登录', 12000);
        }
        $where = [];
        if ($plugin) {
            $where[] = ['plugin', 'in', $plugin];
        } else {
            $where[] = ['plugin', '=', ''];
        }
        // 检测是否系统管理员
        if ($model['is_system'] === '20') {
            // 获取全部菜单数据
            $data  = MenuProvider::menuList($where);
        } else {
            // 获取角色权限
            $rules = self::getWebRoleRules($model['role_id'], $plugin);
            // 获取菜单数据
            $where[] = ['path', 'in', $rules];
            $data = MenuProvider::menuList($where);
        }
        // 解析插件类型菜单
        $data = array_map(function ($item) {
            // 检测斜杠数量
            $pathCount = substr_count($item['path'], '/');
            if ($pathCount >= 1 && $item['plugin'] && $item['plugin'] !== 'admin') {
                $item['path'] = "app/{$item['plugin']}/{$item['path']}";
            }
            return $item;
        }, $data);
        // 解析数据格式
        $data = MenuProvider::parseMenu($data);
        // 返回数据
        return $data;
    }

    /**
     * 获取站点角色权限规则
     * @param int $roleId 角色ID
     * @param string $plugin 插件名称
     * @param bool $refresh 是否刷新缓存
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getWebRoleRules(int $roleId, array $plugin = [], bool $refresh = false)
    {
        $key  = "web_rule_{$roleId}";
        $data = Cache::get($key);
        if ($data && !$refresh) {
            return $data;
        }
        // 获取角色权限
        $data  = WebRole::where('id', $roleId)->value('rule', [], true);
        $rules = [];
        foreach ($data as $path) {
            // 添加自身
            $rules[] = $path;
            // 查询自身是否有父级规则
            $where = [
                ['path', '=', $path],
            ];
            if ($plugin) {
                $where[] = ['plugin', 'in', $plugin];
            } else {
                $where[] = ['plugin', '=', ''];
            }
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