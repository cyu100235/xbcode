<?php
namespace plugin\xbCode\api;

use support\Cache;
use plugin\xbCode\app\model\Admin;
use plugin\xbCode\app\model\AdminRole;
use plugin\xbCode\app\model\AdminRule;

/**
 * 菜单安装/卸载接口
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminMenus
{
    /**
     * 获取管理员菜单
     * @param int $adminId 管理员ID
     * @param string $isWeb 是否总后台菜单
     * @throws \Exception
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get(int $adminId, string $isWeb = '20')
    {
        $model = Admin::where('id', $adminId)->find();
        if (!$model) {
            throw new \Exception('管理员信息错误，请重新登录', 12000);
        }
        $where = [
            ['is_web', '=', $isWeb]
        ];
        // 检测非系统管理员
        if ($model['is_system'] !== '20') {
            // 获取角色菜单规则
            $rules = self::getRoleRules($model['role_id'], $isWeb);
            $where[] = ['path', 'in', $rules];
        }
        // 获取菜单数据
        $data = AdminRule::where($where)->order('sort asc,id asc')->select()->toArray();
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
        $data = MenuChecked::parseMenu($data);
        // 返回数据
        return $data;
    }
    
    /**
     * 获取角色权限
     * @param int $roleId 角色ID
     * @param string $isWeb 是否总后台菜单
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getRoleRules(int $roleId, string $isWeb)
    {
        $key = "admin_rules_{$roleId}";
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
                ['is_web', '=', $isWeb],
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

    /**
     * 获取父级规则
     * @param int $pid 父级菜单ID
     * @param mixed $rules 菜单规则
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getParentRules(int $pid, $rules = [])
    {
        $model = AdminRule::where('id', $pid)->find();
        if (!$model) {
            return $rules;
        }
        $rules[] = $model['path'];
        if ($model['pid'] != 0) {
            return self::getParentRules($model['pid'], $rules);
        } else {
            return $rules;
        }
    }
}