<?php
/**
 * 贵州小白基地网络科技有限公司
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
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
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Menus
{
    
    /**
     * 获取管理员菜单
     * @param int $adminId 管理员ID
     * @param string $isWeb Saas应用菜单，10=系统菜单，20=Saas应用菜单
     * @throws \Exception
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get(int $adminId, string $isWeb = '10')
    {
        $model = Admin::where('id', $adminId)->find();
        if (!$model) {
            throw new Exception('管理员信息错误，请重新登录', 12000);
        }
        $where = [];
        // 检测非系统管理员
        if ($model['is_system'] !== '20') {
            // 获取角色菜单规则
            $rules = self::getRoleRules($model['role_id']);
            $where[] = ['path', 'in', $rules];
        }
        // 获取菜单数据
        $data = AdminRule::where($where)->where('state', '20')->order('sort asc,id asc')->select()->toArray();
        // 解析数据格式
        $data = MenuChecked::parseMenu($data);
        // 返回数据
        return $data;
    }

    /**
     * 获取插件模块菜单数据
     * @param string $plugin
     * @return array
     * @copyright 贵州猿创科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getMenusModule(string $plugin = '')
    {
        $plugin = $plugin ?: Install::getCallPluginName();
        $module = request()->app ?: '';
        if (empty($plugin)) {
            throw new Exception('获取菜单时，插件标识参数错误');
        }
        if (empty($module)) {
            throw new Exception('获取菜单时，插件模块参数错误');
        }
        $menuPath = base_path()."/plugin/{$plugin}/app/{$module}/menus.php";
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
     * 获取角色权限
     * @param int $roleId 角色ID
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getRoleRules(int $roleId)
    {
        $key = "admin_rules_{$roleId}";
        $data = Cache::get($key);
        if ($data) {
            return $data;
        }
        $adminRole  = AdminRole::where('id', $roleId)->find();
        $dataRules = $adminRole['rules'] ?? '';
        if (!$dataRules) {
            return [];
        }
        $isWeb = $adminRole['saas_appid'] ? '20' : '10';
        $rules = [];
        foreach ($dataRules as $path) {
            // 添加自身
            $rules[] = $path;
            // 查询自身是否有父级规则
            $where  = [
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
     * 安装菜单
     * @param array $data 菜单数据
     * @param string $name 插件标识
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function install(array $data, string $name)
    {
        try {
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
                if (!in_array($value['is_show'], [10, 20])) {
                    throw new Exception('是否显示值错误');
                }
                if (empty($value['type'])) {
                    throw new Exception('缺少菜单类型');
                }
                if (!in_array($value['type'], [10, 20, 30])) {
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
                if (empty($value['params'])) {
                    $value['params'] = '';
                }
                if (empty($value['plugin'])) {
                    $value['plugin'] = $name;
                }
                if (empty($value['is_system'])) {
                    $value['is_system'] = '10';
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
                    $children = array_map(function($item)use($menuId){
                        $item['pid'] = $menuId;
                        return $item;
                    }, $value['children']);
                    // 递归添加子级菜单
                    static::install($children, $name);
                }
            }
        } catch (\Throwable $th) {
            throw new Exception("菜单安装失败，{$th->getMessage()}");
        }
    }
    
    /**
     * 卸载菜单
     * @param string $name 插件标识，为空自动识别插件标识
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function uninstall(string $name = null)
    {
        if (empty($name)) {
            $name = Install::getCallPluginName();
        }
        // 获取菜单列表
        $menus = config("plugin.{$name}.menu",[]);
        if (empty($menus)) {
            return true;
        }
        // 多维菜单转二维菜单格式
        $menus = MenuChecked::menuTreeTo2D($menus);
        // 查询所有顶级菜单
        $topMenus = array_values(array_filter($menus, function($value) {
            return empty($value['pid']);
        }));
        // 查询所有子级菜单
        $children = array_values(array_filter($menus, function($value) {
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
        return true;
    }
}