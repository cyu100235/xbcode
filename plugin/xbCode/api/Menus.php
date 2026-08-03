<?php
/**
 * 贵州积木云网络科技有限公司
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use Exception;
use support\think\Cache;
use plugin\xbCode\app\model\Admin;
use plugin\xbCode\app\model\AdminRole;
use plugin\xbCode\app\model\AdminRule;

/**
 * 菜单安装/卸载接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Menus
{
    /**
     * 获取管理员菜单
     * @param int $adminId 管理员ID
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function get(int $adminId)
    {
        $model = Admin::where('id', $adminId)->find();
        if (!$model) {
            throw new Exception('管理员信息错误，请重新登录', 12000);
        }
        $where = [
            ['state', '=', '20'],
        ];
        // 检测非系统管理员
        if ($model['is_system'] !== '20') {
            // 获取角色菜单规则
            $rules = static::getRoleRules($model['role_id'], true);
            $where[] = ['path', 'in', $rules];
        }
        // 获取菜单数据
        $data = AdminRule::where($where)
            ->order('sort asc,id asc')
            ->select()
            ->toArray();
        // 解析数据格式
        $data = MenuChecked::parseMenu($data);
        // 返回数据
        return $data;
    }

    /**
     * 获取菜单文件数据
     * @param string $path
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getMenusPath(string $path)
    {
        if (!file_exists($path)) {
            throw new Exception('菜单文件不存在');
        }
        $data = include $path;
        // 验证菜单数据
        MenuData::validateMenus($data);
        // 解析数据格式
        $menus = MenuChecked::parseMenu($data, true);
        // 返回数据
        return $menus;
    }

    /**
     * 获取模块菜单文件数据
     * @param array $menuPaths 菜单文件路径，示例：['plugin/xbCode/app/user/config/menu.php']
     * @throws Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getModuleMenusPaths(array $menuPaths)
    {
        if (empty($menuPaths)) {
            throw new Exception('菜单文件路径不能为空');
        }
        $menus = [];
        foreach ($menuPaths as $path) {
            if (!file_exists($path)) {
                continue;
            }
            $content = file_get_contents($path);
            if (empty($content)) {
                continue;
            }
            $list = include $path;
            if (!empty($list)) {
                $plugin = basename(dirname($path, 4));
                $list = MenuChecked::resetField($list, 'plugin', $plugin);
            }
            $menus = array_merge($menus, $list);
        }
        // 重设菜单关系
        $menus = MenuChecked::menusNo($menus);
        // 将菜单转为二维数组
        $menus = MenuChecked::menuTreeTo2D($menus);
        // 重新排序
        $menus = list_sort_by($menus, 'sort', 'asc');
        // 将菜单转为树状
        $menus = MenuChecked::menu2DToTree($menus);
        // 重新解析菜单数据
        $menus = MenuChecked::parseMenu($menus, true);
        // 返回数据
        return $menus;
    }

    /**
     * 获取插件模块菜单数据
     * @param string $plugin
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getMenusModule(string $plugin = '')
    {
        $module = request()->app ?: '';
        if (empty($plugin)) {
            throw new Exception('获取菜单时，插件标识参数错误');
        }
        if (empty($module)) {
            throw new Exception('获取菜单时，插件模块参数错误');
        }
        $menuPath = base_path() . "/plugin/{$plugin}/app/{$module}/menus.php";
        if (!file_exists($menuPath)) {
            throw new Exception('插件菜单模块菜单文件不存在');
        }
        $data = include $menuPath;
        // 验证菜单数据
        MenuData::validateMenus($data);
        // 解析菜单数据
        $data = MenuChecked::parseMenu($data, true);
        // 返回数据
        return $data;
    }

    /**
     * 获取角色权限规则
     * @param int $roleId 角色ID
     * @param bool $force 是否强制刷新缓存
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getRoleRules(int $roleId, bool $force = false)
    {
        $key = "admin_rules_{$roleId}";
        $data = Cache::get($key);
        if ($data && !$force) {
            return $data;
        }
        $adminRules = AdminRole::where('id', $roleId)->value('rule', []);
        if (empty($adminRules)) {
            return [];
        }
        $adminRules = json_decode($adminRules, true);
        $rules = [];
        foreach ($adminRules as $path) {
            // 添加自身
            $rules[] = $path;
            // 查询自身是否有父级规则
            $where = [
                ['path', '=', $path],
                ['state', '=', '20'],
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
        Cache::set($key, $rules, 600);
        // 返回数据
        return $rules;
    }

    /**
     * 获取父级规则
     * @param int $pid 父级菜单ID
     * @param mixed $rules 菜单规则
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getParentRules(int $pid, array $rules = [])
    {
        $model = AdminRule::where('id', $pid)->where('state', '20')->find();
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

    /**
     * 处理顶级菜单归属权
     * @param array $menus
     * @param string $name
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function checkTopMenus(array $menus, string $name = 'xbCode')
    {
        // 处理顶级菜单归属权
        return array_map(function ($item) use ($name) {
            if (empty($item['plugin'])) {
                $item['plugin'] = $name;
            }
            return $item;
        }, $menus);
    }

    /**
     * 安装菜单
     * @param array $data 菜单数据
     * @param string $name 插件标识
     * @param int $level 当前层级
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function install(array $data, string $name, int $level = 0)
    {
        // 检测是否已安装saas插件
        if (PluginsApi::make()->installed('xbSaas')) {
            return;
        }
        if ($level === 0) {
            // 处理顶级菜单归属权
            $data = static::checkTopMenus($data);
        }
        foreach ($data as $value) {
            if (empty($value['title'])) {
                throw new Exception('缺少菜单标题');
            }
            if (empty($value['path'])) {
                throw new Exception('缺少地址路径');
            }
            if (empty($value['is_show'])) {
                throw new Exception('缺少是否显示');
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
            if (empty($value['icon'])) {
                $value['icon'] = '';
            }
            // 处理请求参数
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
            if (empty($value['is_show'])) {
                $value['is_show'] = '10';
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
            if (!in_array($value['is_show'], ['10', '20'])) {
                throw new Exception('是否显示值错误');
            }
            if (!in_array($value['state'], ['10', '20'])) {
                throw new Exception('状态值错误');
            }
            // 检测是否菜单是否存在
            $where = [
                'plugin' => $value['plugin'],
                'path' => $value['path'],
            ];
            $model = AdminRule::where($where)->find();
            if ($model && empty($value['children'])) {
                $model->save($value);
                continue;
            }
            // 添加菜单
            if (!$model) {
                $model = new AdminRule;
            }
            if (!$model->save($value)) {
                throw new Exception('菜单保存失败');
            }
            // 是否递归添加
            if (!empty($value['children'])) {
                // 获取父级菜单ID
                $menuId = $model->id;
                // 添加父级菜单ID
                $children = array_map(function ($item) use ($menuId) {
                    $item['pid'] = $menuId;
                    return $item;
                }, $value['children']);
                // 递归添加子级菜单
                static::install($children, $name, $level + 1);
            }
        }
    }

    /**
     * 卸载菜单
     * @param string $name 插件标识
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function uninstall(string $name = '')
    {
        if (empty($name)) {
            throw new Exception('插件标识不能为空');
        }
        // 获取菜单列表
        $menus = config("plugin.{$name}.menu", []);
        if (empty($menus)) {
            return;
        }
        // 处理顶级菜单归属权
        $menus = static::checkTopMenus($menus);
        // 多维菜单转二维菜单格式
        $menus = MenuChecked::menuTreeTo2D($menus);
        // 查询所有顶级菜单
        $topMenus = array_values(array_filter($menus, function ($value) {
            return empty($value['pid']);
        }));
        // 查询所有子级菜单
        $children = array_values(array_filter($menus, function ($value) {
            return !empty($value['pid']);
        }));
        // 删除所有子级菜单
        foreach ($children as $value) {
            $plugin = $value['plugin'] ?? $name;
            $where = [
                'plugin' => $plugin,
                'path' => $value['path'],
            ];
            $model = AdminRule::where($where)->find();
            if (empty($model)) {
                continue;
            }
            // 删除所有子菜单
            $model->delete();
        }
        // 删除父级菜单
        foreach ($topMenus as $value) {
            $plugin = $value['plugin'] ?? $name;
            $where = [
                'plugin' => $plugin,
                'path' => $value['path'],
            ];
            $model = AdminRule::where($where)->find();
            if (empty($model)) {
                continue;
            }
            // 检测是否有子菜单
            $children = AdminRule::where('pid', $model->id)->count();
            if ($children) {
                continue;
            }
            $model->delete();
        }
    }
}