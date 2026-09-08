<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use Exception;
use plugin\xbAdmin\app\model\Admin;
use plugin\xbAdmin\app\model\AdminRole;
use plugin\xbAdmin\app\model\AdminRule;
use plugin\xbAdmin\enum\StateEnum;
use plugin\xbAdmin\enum\YesEnum;
use plugin\xbAdmin\exception\business\ExceptionUnauthorized;

/**
 * 菜单（权限规则）接口
 *
 * 负责菜单的下发、角色权限解析，以及插件安装/卸载时与 config/menu.php 的同步。
 * 这里不做规则缓存，角色权限每次直接查库，保证后台改动即时生效。
 */
class Menus
{
    /**
     * 获取管理员可见菜单
     * @param int $adminId 管理员ID
     * @throws ExceptionUnauthorized
     * @return array
     */
    public static function get(int $adminId): array
    {
        $model = Admin::where('id', $adminId)->find();
        if (!$model) {
            throw new ExceptionUnauthorized('登录信息已失效，请重新登录');
        }
        $where = [
            ['state', '=', StateEnum::ENABLED['value']],
        ];
        // 非超级管理员按角色权限过滤
        if ((string) $model['is_system'] !== YesEnum::YES['value']) {
            $rules = static::getRoleRules((int) $model['role_id']);
            if (empty($rules)) {
                return [];
            }
            $where[] = ['path', 'in', $rules];
        }
        $data = AdminRule::where($where)
            ->order('sort asc,id asc')
            ->select()
            ->toArray();
        return MenuChecked::parseMenu($data);
    }

    /**
     * 获取角色的全部权限标识（含父级链）
     * @param int $roleId 角色ID
     * @return array
     */
    public static function getRoleRules(int $roleId): array
    {
        if ($roleId <= 0) {
            return [];
        }
        $rule = AdminRole::where('id', $roleId)->value('rule');
        $paths = AdminRole::parseRules($rule);
        if (empty($paths)) {
            return [];
        }
        $rules = [];
        foreach ($paths as $path) {
            // 权限自身
            $rules[] = $path;
            // 逐级补全父级，保证菜单树可以完整渲染
            $pid = AdminRule::where([
                ['path', '=', $path],
                ['state', '=', StateEnum::ENABLED['value']],
            ])->value('pid');
            if ((int) $pid !== 0) {
                $rules = array_merge($rules, static::getParentRules((int) $pid));
            }
        }
        return array_values(array_unique($rules));
    }

    /**
     * 逐级获取父级权限标识
     * @param int $pid 父级菜单ID
     * @param array $rules 已收集的权限标识
     * @return array
     */
    public static function getParentRules(int $pid, array $rules = []): array
    {
        $model = AdminRule::where('id', $pid)->where('state', StateEnum::ENABLED['value'])->find();
        if (!$model) {
            return $rules;
        }
        $rules[] = $model['path'];
        if ((int) $model['pid'] !== 0) {
            return static::getParentRules((int) $model['pid'], $rules);
        }
        return $rules;
    }

    /**
     * 处理顶级菜单的插件归属
     * @param array $menus
     * @param string $name 插件标识
     * @return array
     */
    public static function checkTopMenus(array $menus, string $name = 'xbAdmin'): array
    {
        return array_map(function ($item) use ($name) {
            if (empty($item['plugin'])) {
                $item['plugin'] = $name;
            }
            return $item;
        }, $menus);
    }

    /**
     * 安装菜单（写入 config/menu.php 定义的权限）
     * @param array $data 菜单数据
     * @param string $name 插件标识
     * @param int $level 当前层级
     * @throws Exception
     * @return void
     */
    public static function install(array $data, string $name = 'xbAdmin', int $level = 0): void
    {
        if (empty($data)) {
            return;
        }
        if ($level === 0) {
            $data = static::checkTopMenus($data, $name);
        }
        foreach ($data as $value) {
            if (empty($value['title'])) {
                throw new Exception('缺少菜单标题');
            }
            if (empty($value['path'])) {
                throw new Exception('缺少地址路径');
            }
            if (empty($value['is_show'])) {
                $value['is_show'] = '10';
            }
            if (!in_array($value['is_show'], ['10', '20'])) {
                throw new Exception('是否显示值错误');
            }
            if (empty($value['type'])) {
                throw new Exception('缺少菜单类型');
            }
            if (!in_array($value['type'], ['10', '20', '30'])) {
                throw new Exception('菜单类型错误');
            }
            // 默认值
            if (empty($value['pid'])) {
                $value['pid'] = 0;
            }
            if (empty($value['sort'])) {
                $value['sort'] = 0;
            }
            if (!isset($value['icon'])) {
                $value['icon'] = '';
            }
            if (empty($value['method'])) {
                $value['method'] = 'GET';
            }
            if (is_array($value['method'])) {
                $value['method'] = implode(',', $value['method']);
            }
            if (empty($value['params'])) {
                $value['params'] = '';
            }
            if (is_array($value['params'])) {
                $value['params'] = json_encode($value['params'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            if (empty($value['plugin'])) {
                $value['plugin'] = $name;
            }
            if (empty($value['is_system'])) {
                $value['is_system'] = '10';
            }
            if (empty($value['is_default'])) {
                $value['is_default'] = '10';
            }
            if (empty($value['state'])) {
                $value['state'] = '10';
            }
            if (!in_array($value['is_default'], ['10', '20'])) {
                throw new Exception('是否默认值错误');
            }
            if (!in_array($value['is_system'], ['10', '20'])) {
                throw new Exception('是否系统菜单值错误');
            }
            if (!in_array($value['state'], ['10', '20'])) {
                throw new Exception('状态值错误');
            }
            // children 不是数据表字段，入库前必须剔除
            $children = $value['children'] ?? [];
            unset($value['children']);
            // 相同插件 + 相同权限标识视为同一条菜单
            $model = AdminRule::where([
                'plugin' => $value['plugin'],
                'path' => $value['path'],
            ])->find();
            if ($model) {
                if (!$model->save($value)) {
                    throw new Exception('菜单保存失败：' . $value['title']);
                }
            } else {
                $model = new AdminRule;
                if (!$model->save($value)) {
                    throw new Exception('菜单保存失败：' . $value['title']);
                }
            }
            if (!empty($children)) {
                $menuId = (int) $model->id;
                $children = array_map(function ($item) use ($menuId) {
                    $item['pid'] = $menuId;
                    return $item;
                }, $children);
                static::install($children, $name, $level + 1);
            }
        }
    }

    /**
     * 卸载菜单（仅删除 config/menu.php 中声明的权限）
     * @param string $name 插件标识
     * @throws Exception
     * @return void
     */
    public static function uninstall(string $name = 'xbAdmin'): void
    {
        if ($name === '') {
            throw new Exception('插件标识不能为空');
        }
        $menus = config("plugin.{$name}.menu", []);
        if (empty($menus)) {
            return;
        }
        $menus = static::checkTopMenus($menus, $name);
        $menus = MenuChecked::menuTreeTo2D($menus);
        $topMenus = [];
        $children = [];
        foreach ($menus as $value) {
            if (empty($value['pid'])) {
                $topMenus[] = $value;
            } else {
                $children[] = $value;
            }
        }
        // 先删子级，避免父级被残留子级挡住
        foreach ($children as $value) {
            static::deleteMenu($value, $name);
        }
        foreach ($topMenus as $value) {
            $model = static::deleteMenu($value, $name);
            if (!$model) {
                continue;
            }
            if (AdminRule::where('pid', $model->id)->count() > 0) {
                continue;
            }
            $model->delete();
        }
    }

    /**
     * 删除单条菜单，返回命中的模型
     * @param array $value
     * @param string $name
     * @return AdminRule|null
     */
    protected static function deleteMenu(array $value, string $name): ?AdminRule
    {
        if (empty($value['path'])) {
            return null;
        }
        $model = AdminRule::where([
            'plugin' => $value['plugin'] ?? $name,
            'path' => $value['path'],
        ])->find();
        if (empty($model)) {
            return null;
        }
        return $model;
    }
}
