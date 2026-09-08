<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use plugin\xbAdmin\app\model\AdminRule;

/**
 * 管理员权限校验接口
 *
 * 权限口径：以「模块/控制器/方法」（如 admin/AdminRole/index）作为权限标识，
 * 与 admin_rule.path、xbAdminPathInfo() 的返回值完全一致。
 */
class AdminAuthApi
{
    /**
     * 创建实例
     * @return static
     */
    public static function make(): static
    {
        return new static;
    }

    /**
     * 检测角色是否拥有指定权限
     * @param int $roleId 角色ID
     * @param string $path 权限标识，如 admin/AdminRole/index
     * @return bool
     */
    public function Authentication(int $roleId, string $path): bool
    {
        if ($path === '' || $roleId <= 0) {
            return false;
        }
        $rules = Menus::getRoleRules($roleId);
        if (empty($rules)) {
            return false;
        }
        return in_array($path, $rules, true);
    }

    /**
     * 检测权限标识是否已在菜单中定义
     * @param string $path 权限标识
     * @return bool
     */
    public function hasRule(string $path): bool
    {
        if ($path === '') {
            return false;
        }
        return AdminRule::where('path', $path)->count() > 0;
    }
}
