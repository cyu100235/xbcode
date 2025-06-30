<?php
namespace plugin\xbCode\api;

use Exception;
use plugin\xbCode\app\model\AdminRule;

/**
 * 菜单安装/卸载接口
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Menus
{
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
                if (empty($value['is_saas'])) {
                    $value['is_saas'] = '20';
                }
                if (!class_exists('plugin\xbSaas\api\Install')) {
                    $value['is_saas'] = '10';
                }
                if (!in_array($value['is_saas'], ['10', '20'])) {
                    throw new Exception('菜单是否站点类型错误');
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
                    'is_saas' => $value['is_saas'],
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