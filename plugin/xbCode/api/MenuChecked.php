<?php
namespace plugin\xbCode\api;

use Exception;
use plugin\xbCode\utils\DataUtil;
use plugin\xbCode\app\validate\AdminRuleValidate;

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
     * 过滤菜单字段
     * @param array $data
     * @param array $fields
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function unsetMenusFields(array $data, array $fields)
    {
        $i = 0;
        $list = [];
        foreach ($data as $menu) {
            foreach ($menu as $field => $value) {
                if (in_array($field, $fields)) {
                    unset($menu[$field]);
                }
            }
            $list[$i] = $menu;
            if (isset($menu['children'])) {
                $list[$i]['children'] = self::unsetMenusFields($menu['children'], $fields);
            }
            $i++;
        }
        return $list;
    }

    /**
     * 序列化菜单格式数据
     * @param array $data
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function serializeMenus(array $data)
    {
        foreach ($data as $key => $value) {
            xbValidate(AdminRuleValidate::class, $value, 'add');
            if (empty($value['method'])) {
                $value['method'] = 'GET';
            }
            $value['method'] = is_array($value['method']) ? implode(',', $value['method']) : 'GET';
            $data[$key] = [
                'id' => $value['id'],
                'title' => $value['title'],
                'plugin' => $value['plugin'],
                'path' => $value['path'],
                'type' => $value['type'],
                'icon' => $value['icon'],
                'params' => $value['params'],
                'is_show' => $value['is_show'],
                'is_system' => $value['is_system'],
                'is_saas' => $value['is_saas'] ?? '10',
                'sort' => $value['sort'] ?? 0,
            ];
            if (isset($value['id'])) {
                $data[$key]['id'] = $value['id'];
            }
            if (isset($value['pid'])) {
                $data[$key]['pid'] = $value['pid'];
            }
        }
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
                $data = array_merge($data, self::menuTreeTo2D($value['children'], $id, $id + 1));
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
        if (empty($data['method'])) {
            $data['method'] = 'GET';
        }
        if (empty($data['plugin'])) {
            $data['plugin'] = request()->plugin ?? 'xbCode';
        }
        // 替换路径
        if(!str_contains($data['path'], 'workbench')){
            $data['path'] = "app/{$data['plugin']}/{$data['path']}";            
        }
        // 设置菜单请求类型
        $data['method'] = is_array($data['method']) ? current($data['method']) : 'GET';
        // 设置图标
        $data['icon'] = $data['icon'] ?? '';
        // 设置是否系统菜单
        $data['is_system'] = (string)($data['is_system'] ?? '10');
        // 是否默认菜单
        $data['is_default'] = (string)($data['is_default'] ?? '10');
        // 是否显示菜单
        $data['is_show'] = (string)($data['is_show'] ?? '10');
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