<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\api;

use Exception;
use plugin\xbAdmin\utils\DataUtil;

/**
 * 菜单数据解析
 *
 * 数据库中的 path 存「模块/控制器/方法」（如 admin/Admin/index），
 * 下发给前端时需要补全成「插件/模块/控制器/方法」（如 xbAdmin/admin/Admin/index）。
 *
 * 前端约定（依据 public/backend/assets 构建产物反查）：
 * 1. 路由地址 = '/' + path，故 path 必须全局唯一且不含前导斜杠；
 * 2. path 同时作为 amis schema 的取数地址，axios 的 baseURL 固定为 window.location.origin，
 *    最终请求 {origin}/xbAdmin/admin/Admin/index，即跨插件路由，
 *    因此不能带 app/ 前缀（那是静态资源目录映射规则，不是路由）；
 * 3. 工作台菜单不参与上述拼接，其 path 恒为 workbench（前端布局路由 redirect 硬编码该值），
 *    取数地址改由 params.url 提供。
 */
class MenuChecked
{
    /**
     * 解析菜单数据
     * @param array $data 菜单数据
     * @param bool $isLevel 数据是否为树状结构
     * @return array
     */
    public static function parseMenu(array $data, bool $isLevel = false): array
    {
        if ($isLevel) {
            // 树状转二维数组
            $data = static::menuTreeTo2D($data);
        }
        // 二维数组转树状
        $data = static::menu2DToTree($data);
        // 解析菜单数据
        $data = static::parseMenusTreeData($data);
        // 生成菜单唯一标识
        $data = static::getMenuKey($data);
        // 重置多层级数组下标
        return static::resetKeys($data);
    }

    /**
     * 递归剔除菜单字段
     * @param array $data
     * @param array $fields
     * @return array
     */
    public static function unsetMenusFields(array $data, array $fields): array
    {
        $list = [];
        foreach ($data as $menu) {
            foreach ($menu as $field => $value) {
                if (in_array($field, $fields, true)) {
                    unset($menu[$field]);
                }
            }
            if (isset($menu['children']) && is_array($menu['children'])) {
                $menu['children'] = static::unsetMenusFields($menu['children'], $fields);
            }
            $list[] = $menu;
        }
        return $list;
    }

    /**
     * 二维数组转树形结构
     * @param array $data
     * @return array
     */
    public static function menu2DToTree(array $data): array
    {
        return DataUtil::channelLevel($data, 0, '', 'id', 'pid');
    }

    /**
     * 树形结构转二维数组
     * @param array $menus
     * @param int $pid 顶级父级ID
     * @param int $id 起始自增ID
     * @return array
     */
    public static function menuTreeTo2D(array $menus, int $pid = 0, int $id = 1): array
    {
        $data = [];
        $counter = $id;
        static::flattenTree($menus, $pid, $counter, $data);
        return $data;
    }

    /**
     * 展平树形菜单
     * @param array $menus
     * @param int $pid
     * @param int $counter 自增ID引用
     * @param array $data 结果引用
     * @return void
     */
    protected static function flattenTree(array $menus, int $pid, int &$counter, array &$data): void
    {
        foreach ($menus as $value) {
            $children = $value['children'] ?? [];
            unset($value['children']);
            if (empty($value['id'])) {
                $value['id'] = $counter;
            }
            if (!isset($value['pid'])) {
                $value['pid'] = $pid;
            }
            $data[] = $value;
            $current = (int) $value['id'];
            $counter = max($counter, $current) + 1;
            if (!empty($children)) {
                static::flattenTree($children, $current, $counter, $data);
            }
        }
    }

    /**
     * 为树形菜单补全自增的id与pid
     * @param array $menus
     * @param int $pid
     * @param int $id
     * @return array
     */
    public static function menusNo(array $menus, int $pid = 0, int $id = 1): array
    {
        foreach ($menus as $key => $value) {
            $menus[$key]['pid'] = $pid;
            $menus[$key]['id'] = $id;
            if (!empty($value['children'])) {
                $menus[$key]['children'] = static::menusNo($value['children'], $id, $id + 1);
                $children = $menus[$key]['children'];
                $last = end($children);
                if (is_array($last) && isset($last['id'])) {
                    $id = (int) $last['id'];
                }
            }
            $id++;
        }
        return $menus;
    }

    /**
     * 递归重设菜单字段值
     * @param array $menus
     * @param string $field
     * @param string $value
     * @throws Exception
     * @return array
     */
    public static function resetField(array $menus, string $field, string $value): array
    {
        if (empty($menus)) {
            throw new Exception('重设字段失败，菜单数据错误');
        }
        foreach ($menus as &$menu) {
            $menu[$field] = $value;
            if (!empty($menu['children'])) {
                $menu['children'] = static::resetField($menu['children'], $field, $value);
            }
        }
        return $menus;
    }

    /**
     * 重置多层级数组的下标
     * @param mixed $array
     * @return mixed
     */
    public static function resetKeys(mixed $array): mixed
    {
        return DataUtil::resetKeys($array);
    }

    /**
     * 解析树状菜单数据
     * @param array $data
     * @return array
     */
    protected static function parseMenusTreeData(array $data): array
    {
        foreach ($data as &$value) {
            $value = static::parseMenuData($value);
            if (!empty($value['children'])) {
                $value['children'] = static::parseMenusTreeData($value['children']);
            }
        }
        return $data;
    }

    /**
     * 解析单条菜单数据
     * @param array $data
     * @throws Exception
     * @return array
     */
    protected static function parseMenuData(array $data): array
    {
        if (!isset($data['id'])) {
            throw new Exception('菜单必须包含id字段');
        }
        if (!isset($data['pid'])) {
            throw new Exception('菜单必须包含pid字段');
        }
        if (empty($data['plugin'])) {
            $data['plugin'] = (string) (request()->plugin ?? 'xbAdmin');
        }
        $params = $data['params'] ?? '';
        if (!is_array($params)) {
            $params = json_decode((string) $params, true);
            $params = is_array($params) ? $params : [];
        }
        $path = (string) ($data['path'] ?? '');
        // 工作台类型菜单，params.url 指向远程视图
        if ($path === 'workbench') {
            $url = $params['url'] ?? '';
            $params = "{$data['plugin']}/{$url}";
        } else {
            // 普通菜单补全插件前缀
            if ($path !== '' && !str_contains($path, 'workbench') && str_contains($path, '/')) {
                $data['path'] = "{$data['plugin']}/{$path}";
            }
            // 前端用「params ?? path」推导取数地址，并把 params 当字符串解析，
            // 空参数必须下发 null 才能回退到 path（空字符串不触发回退，对象/数组会解析异常）
            $params = null;
        }
        $method = $data['method'] ?? '';
        if (is_array($method)) {
            $method = current($method) ?: 'GET';
        }
        $data['method'] = $method !== '' ? strtoupper((string) $method) : 'GET';
        $data['params'] = $params;
        $data['icon'] = $data['icon'] ?? '';
        $data['short_title'] = $data['short_title'] ?? '';
        $data['is_system'] = (string) ($data['is_system'] ?? '10');
        $data['is_default'] = (string) ($data['is_default'] ?? '10');
        $data['is_show'] = (string) ($data['is_show'] ?? '10');
        return $data;
    }

    /**
     * 生成树状菜单的唯一标识
     * @param array $data
     * @param string $key
     * @return array
     */
    protected static function getMenuKey(array $data, string $key = ''): array
    {
        foreach ($data as &$value) {
            $menuKey = empty($value['menu_key']) ? $value['id'] : $value['menu_key'];
            $value['menu_key'] = $key === '' ? (string) $menuKey : "{$key}-{$menuKey}";
            if (!empty($value['children'])) {
                $value['children'] = static::getMenuKey($value['children'], $value['menu_key']);
            }
        }
        return $data;
    }
}
