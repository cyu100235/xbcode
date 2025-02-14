<?php
namespace plugin\xbCode\api;

use Exception;
use plugin\xbCode\utils\DataUtil;

/**
 * 菜单数据处理接口
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MenuChecked
{
    /**
     * 解析菜单数据
     * @param array $data 菜单数据
     * @param bool $isLevel 数据是否树状结构
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function parseMenu(array $data, bool $isLevel = false)
    {
        if ($isLevel) {
            // 树状转二维数组
            $data = self::menuTreeTo2D($data);            
        }
        // 二维数组转树状
        $data = self::menu2DToTree($data);
        // 解析菜单数据
        $data = self::parseMenusTreeData($data);
        // 获取菜单KEY
        $data = self::getMenuKey($data);
        // 重置多层级数组的下标
        $data = self::resetKeys($data);
        // 返回菜单数据
        return $data;
    }

    /**
     * 二维数组转树形结构
     * @param array $menus 二维菜单数组
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function menu2DToTree(array $data)
    {
        $data = DataUtil::channelLevel($data, 0, '', 'id', 'pid');
        return $data;
    }
    
    /**
     * 树形结构转二维数组
     * @param array $menus 树形菜单数组
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function menuTreeTo2D(array $menus, int $pid = 0, int $id = 1)
    {
        $data = [];
        foreach ($menus as $value) {
            $temp = $value;
            if (empty($temp['id'])) {
                $temp['id'] = $id;
            }
            if (empty($temp['pid'])) {
                $temp['pid'] = $pid;
            }
            unset($temp['children']);
            $data[] = $temp;
            if (!empty($value['children'])) {
                $data = array_merge($data, self::menuTreeTo2D($value['children'],$id, $id + 1));
                $id = end($data)['id'];
            }
            $id++;
        }
        return $data;
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