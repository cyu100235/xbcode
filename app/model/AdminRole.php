<?php

namespace app\model;

use Exception;
use support\Cache;
use xbcode\Model;

/**
 * 管理员角色模型
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AdminRole extends Model
{
    // 设置JSON字段转换
    protected $json = ['rule'];
    // 设置JSON数据返回数组
    protected $jsonAssoc = true;
    
    /**
     * 检查是否有权限
     * @param int $adminId 管理员ID
     * @param string $path 路由地址
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checkAuth(int $adminId, string $path)
    {
        $rules = self::getRoleRule($adminId);
        if (!in_array($path, $rules)) {
            return false;
        }
        return true;
    }

    /**
     * 获取角色权限
     * @param int $adminId
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getRoleRule(int $adminId)
    {
        $key = "admin_rule_{$adminId}";
        $rules = Cache::get($key);
        if ($rules) {
            return $rules;
        }
        $roleId = Admin::where('id', $adminId)->value('role_id');
        if (!$roleId) {
            throw new Exception('管理员不存在', 404);
        }
        $rules = self::where('id', $roleId)->value('rule', [], true);
        if (empty($rules)) {
            return [];
        }
        Cache::set($key, $rules, 300);
        return $rules;
    }
}
