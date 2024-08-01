<?php
namespace app\common\providers;

use app\common\providers\menus\CascaderTrait;
use app\common\providers\menus\MenuActionTrait;
use app\common\utils\DataUtil;
use Exception;

/**
 * 菜单服务提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MenuProvider
{
    use MenuActionTrait;
    use CascaderTrait;

    /**
     * 获取默认规则
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getDefaultRule()
    {
        $data = self::getMenus(['is_default' => '20'])->column('path');
        return $data;
    }

    /**
     * 获取解析数据的菜单数据
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getParseMenus()
    {
        // 获取原始菜单数据
        $data = self::menuList();
        // 解析菜单数据
        $data = self::parseData($data);
        // 返回菜单数据
        return $data;
    }

    /**
     * 解析菜单数据格式
     * @param mixed $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function parseData(array $data)
    {
        foreach ($data as &$menu) {
            $menu['show'] = $menu['is_show'] === '20' ? true : false;
            // 设置菜单路径
            $pluginName = '';
            $moduleName = '';
            if (!empty($menu['plugin_name'])) {
                $pluginName = "app/{$menu['plugin_name']}/";
            }
            if (!empty($menu['module_name'])) {
                $moduleName = "{$menu['module_name']}/";
            }
            $menu['path'] = "{$pluginName}{$moduleName}{$menu['path']}";
        }
        // 重新排序
        $data = list_sort_by($data, 'sort', 'asc');
        // 返回数据
        return $data;
    }

    /**
     * 根据路径获取菜单数据
     * @param string $path
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getPathMenus(string $path)
    {
        // 获取文件后缀
        $suffix = pathinfo($path, PATHINFO_EXTENSION);
        $menus = [];
        if ($suffix === 'php') {
            $menus = require $path;
        }
        if ($suffix === 'json') {
            $menus = json_decode(file_get_contents($path), true);
        }
        if (empty($menus)) {
            throw new Exception('获取菜单数据失败');
        }
        // 解析菜单数据
        return self::parseMenusData($menus);
    }

    /**
     * 解析菜单数据
     * @param array $menus
     * @param array $data
     * @param int $pid
     * @param int $id
     * @return array|object
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function parseMenusData(array $menus, array $data = [], int $pid = 0, int $id = 1)
    {
        $defaultState = '10';
        foreach ($menus as $value) {
            // 设置临时处理数据
            $menuData = $value;
            // 设置菜单ID
            $menuData['id'] = $id;
            // 设置父级ID
            $menuData['pid'] = $pid;
            // 设置图标
            $menuData['icon'] = isset($value['icon']) ? $value['icon'] : '';
            // 菜单组件
            $menuData['component'] = isset($value['component']) ? $value['component'] : 'none/index';
            // 设置默认附带参数
            $menuData['auth_params'] = isset($value['auth_params']) ? $value['auth_params'] : '';
            // 设置是否系统菜单
            $menuData['is_system'] = isset($value['is_system']) ? $value['is_system'] : $defaultState;
            // 是否默认菜单
            $menuData['is_default'] = isset($value['is_default']) ? $value['is_default'] : $defaultState;
            // 菜单是否显示
            $menuData['show'] = isset($value['show']) && $value['show'] === '20' ? true : false;
            // 删除子级菜单
            if (isset($menuData['children'])) {
                unset($menuData['children']);
            }
            // 设置菜单数据
            $data[] = $menuData;
            // 递归处理子级菜单
            if (isset($value['children']) && !empty($value['children'])) {
                $data = array_merge($data, self::parseMenusData($value['children'], [], $id, $id + 1));
                $id = end($data)['id'];
            }
            $id++;
        }
        return $data;
    }

    /**
     * 获取树状菜单数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getMenuTree()
    {
        // 获取原始菜单数据
        $data = self::menuList();
        // 重组菜单数据位树状
        $data = DataUtil::channelLevel($data, 0, '', 'id', 'pid');
        // 返回菜单数据
        return $data;
    }
}