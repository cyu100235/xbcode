<?php
namespace xbcode\providers\menus;

use app\model\AdminRule;
use Exception;

/**
 * 菜单数据处理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait MenuDataTrait
{
    /**
     * 递归获取子菜单ID
     * @param int $id
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getChildrenIds(int $id)
    {
        $ids      = [$id];
        $children = AdminRule::where('pid', $id)->column('id');
        foreach ($children as $child) {
            $ids = array_merge($ids, self::getChildrenIds($child));
        }
        return $ids;
    }

    /**
     * 解析菜单数据
     * @param array $menus
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function parseMenuData(array $data)
    {
        if (!isset($data['id'])) {
            throw new Exception('菜单必须包含id字段');
        }
        if (!isset($data['pid'])) {
            throw new Exception('菜单必须包含pid字段');
        }
        // 替换路径
        $prefix = "";
        if (!empty($value['plugin_name'])) {
            $prefix .= "app/{$data['plugin_name']}/";
        }
        if (!empty($value['module_name'])) {
            $prefix .= "{$data['module_name']}/";
        }
        // 设置路径
        $data['path'] = $prefix . $data['path'];
        // 设置菜单请求类型
        $data['method'] = is_array($data['method']) ? current($data['method']) : 'GET';
        // 设置图标
        $data['icon'] = isset($data['icon']) ? $data['icon'] : '';
        // 菜单组件
        $data['component'] = isset($data['component']) ? $data['component'] : 'none/index';
        // 设置是否系统菜单
        $data['is_system'] = isset($data['is_system']) ? $data['is_system'] : '10';
        // 是否默认菜单
        $data['is_default'] = isset($data['is_default']) ? $data['is_default'] : '10';
        // 菜单是否显示
        $data['show'] = isset($data['is_show']) && $data['is_show'] === '20' ? true : false;
        // 返回数据
        return $data;
    }

    /**
     * 树状菜单获取MENU_KEY
     * @param array $data
     * @param string $key
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getMenuKey(array $data, string $key = '')
    {
        foreach ($data as &$value) {
            $value['menu_key'] = empty($value['menu_key']) ? $value['id'] : $value['menu_key'];
            if (empty($key)) {
                $value['menu_key'] = (string) $value['id'];
            } else {
                $value['menu_key'] = "{$key}-{$value['menu_key']}";
            }
            if (!empty($value['children'])) {
                $value['children'] = self::getMenuKey($value['children'], $value['menu_key']);
            }
        }
        return $data;
    }

    /**
     * 重置多层级数组的下标
     * @param mixed $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function resetKeys($array)
    {
        if (!is_array($array)) {
            return $array;
        }
        $keys = implode('', array_keys($array));
        if (is_numeric($keys)) {
            $array = array_values($array);
        }
        return array_map([self::class, 'resetKeys'], $array);
    }

    /**
     * 解析树状菜单数据
     * @param array $data
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function parseMenusTreeData(array $data)
    {
        foreach ($data as &$value) {
            $value = self::parseMenuData($value);
            if (!empty($value['children'])) {
                $value['children'] = self::parseMenusTreeData($value['children']);
            }
        }
        return $data;
    }
}