<?php

namespace app\common\utils;

use app\common\enum\MenuComponent;
use Exception;

/**
 * 菜单工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MenusUtil
{
    /**
     * 获取默认规则
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getDefaultRule()
    {
        $menus = self::getMenus();
        $data  = [];
        foreach ($menus as $value) {
            if ($value['is_default'] === '20') {
                array_push($data, $value['path']);
            }
        }
        return $data;
    }

    /**
     * 获取菜单最新ID
     * @param array $menus
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function getMenuId(array $menus = [])
    {
        if (empty($menus)) {
            $menus = self::getMenus();
        }
        $menus = list_sort_by($menus, 'id', 'desc');
        $id    = $menus[0]['id'] ?? 0;
        if (!$id) {
            throw new Exception('菜单ID获取失败');
        }
        return $id;
    }

    /**
     * 保存菜单数据
     * @param array $data
     * @param int $id
     * @param string $xBaseName
     * @return int
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function save(array $data, int $id = null)
    {
        if (empty($data)) {
            throw new Exception('菜单数据不能为空');
        }
        // 处理父级菜单
        if (!empty($data['pid']) && is_array($data['pid'])) {
            $data['pid'] = end($data['pid']);
        }
        // 路由地址首字母转大写
        $data['path'] = ucfirst($data['path']);
        // 设置菜单参数
        $data['auth_params'] = isset($data['auth_params']) ? $data['auth_params'] : '';
        // 获取菜单数据
        $menus = self::getMenus(true);
        if ($id) {
            // 修改菜单数据
            $arrayIndex         = array_search($id, array_column($menus, 'id'));
            $data['id']         = $id;
            $menus[$arrayIndex] = $data;
        } else {
            // 新增菜单数据
            $id = self::getMenuId($menus);
            $id++;
            $data['id'] = $id;
            array_push($menus, $data);
        }
        // 保存菜单数据
        self::saveMenusData($menus);
        return $id;
    }

    /**
     * 批量保存菜单数据
     * @param array $data
     * @param string $xBaseName
     * @return void
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function saveAll(array $data, )
    {
        foreach ($data as $value) {
            self::save($value, null);
        }
    }

    /**
     * 保存菜单数据
     * @param array $menus
     * @param string $xBaseName
     * @return void
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function saveMenusData(array $menus, )
    {
        // 还原菜单格式数据
        $menus = DataUtil::channelLevel($menus, 0, '', 'id', 'pid');
        // 递归处理菜单
        $menus = self::checkData($menus);
        // 转换菜单JSON格式数据
        $menus = json_encode($menus, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        // 获取菜单保存路径
        $menuPath = self::getMenuPath();
        // 储存菜单数据
        file_put_contents($menuPath, $menus);
    }

    /**
     * 删除菜单数据
     * @param int $id
     * @param mixed $xBaseName
     * @return void
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function del(int $id)
    {
        if (empty($id)) {
            throw new Exception('参数错误');
        }
        // 获取菜单数据
        $menus      = self::getMenus(true);
        $arrayIndex = array_search($id, array_column($menus, 'id'));
        $detail     = isset($menus[$arrayIndex]) ? $menus[$arrayIndex] : [];
        if (empty($detail)) {
            throw new Exception('菜单数据不存在');
        }
        // 删除元数据
        unset($menus[$arrayIndex]);
        // 保存菜单数据
        self::saveMenusData($menus);
    }

    /**
     * 递归处理数据
     * @param array $data
     * @return array
     * @author John
     */
    private static function checkData(array $data)
    {
        $data = array_values($data);
        foreach ($data as $key => $value) {
            if (isset($value['_level'])) {
                unset($data[$key]['_level']);
            }
            if (isset($value['_html'])) {
                unset($data[$key]['_html']);
            }
            if (!empty($value['children'])) {
                $data[$key]['children'] = self::checkData($value['children']);
            }
        }
        return $data;
    }

    /**
     * 获取二维菜单数据
     * @param string $xBaseName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getMenus(bool $origin = false)
    {
        // 获得菜单数据
        if ($origin) {
            $data = self::getOriginMenus();
        } else {
            $data = self::getMenuData();
        }
        // 处理菜单数据
        $data = self::parseMenus($data);
        // 处理可展示数据
        foreach ($data as $key => $value) {
            if (!$origin) {
                $value['show']      = $value['show'] == '20' ? '1' : '0';
                $value['is_system'] = $value['is_system'] == '20' ? '1' : '0';
                $value['component'] = $value['component'] == 'none/index' ? '' : $value['component'];
                if (is_array($value['method'])) {
                    $value['method'] = current($value['method']);
                }
            }
            $data[$key] = $value;
        }
        // 返回数据
        return $data;
    }

    /**
     * 获取菜单详情
     * @param int $id
     * @param string $xBaseName
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function find(int $id)
    {
        $data       = self::getMenus(true);
        $arrayIndex = array_search($id, array_column($data, 'id'));
        $detail     = $data[$arrayIndex] ?? [];
        if (empty($detail)) {
            throw new Exception('菜单数据不存在');
        }
        return $detail;
    }

    /**
     * 获取菜单详情
     * @param string $name
     * @param string $value
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function findWhere(string $name,string $value)
    {
        $data = self::getMenus(true);
        $arrayIndex = array_search($value, array_column($data, $name));
        if (!$arrayIndex) {
            return [];
        }
        $detail = $data[$arrayIndex] ?? [];
        return $detail;
    }

    /**
     * 获取菜单组件选项
     * @param string $xBaseName
     * @return array
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function getCascaderOptions()
    {
        $data = self::getMenus(true);
        $data = list_sort_by($data, 'sort', 'asc');
        $data = DataUtil::channelLevel($data, 0, '', 'id', 'pid');
        $data = self::getChildrenOptions($data);
        $data = array_merge([
            [
                'label' => '顶级权限菜单',
                'value' => 0
            ]
        ], $data);
        return $data;
    }

    /**
     * 递归拼接cascader组件数据
     * @Author 贵州小白基地网络科技有限公司
     * @Email 416716328@qq.com
     * @DateTime 2023-03-07
     * @param  array $data
     * @return array
     */
    public static function getChildrenOptions(array $data): array
    {
        $list = [];
        $i    = 0;
        foreach ($data as $value) {
            $componentText        = MenuComponent::getLabel($value['component']);
            $title                = "{$value['title']}-{$componentText}";
            $list[$i]['label']    = $title;
            $list[$i]['value']    = $value['id'];
            $list[$i]['disabled'] = false;
            if (!empty($value['disabled'])) {
                $list[$i]['disabled'] = true;
            }
            if ($value['children']) {
                $list[$i]['children'] = self::getChildrenOptions($value['children']);
            }
            $i++;
        }
        return $list;
    }

    /**
     * 获取菜单路径
     * @param string $xBaseName
     * @return string
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function getMenuPath()
    {
        $request   = request();
        $menuPath  = app_path() . 'menus.json';
        $xBaseName = $request->appName;
        $xBaseModulePath = $request->xBaseModulePath;
        if ($xBaseName && $xBaseModulePath) {
            $menuPath = "{$xBaseModulePath}menus.json";
        }
        return $menuPath;
    }

    /**
     * 获取菜单原始数据
     * @param string $xBaseName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getOriginMenus()
    {
        $menuPath = self::getMenuPath();
        if (!file_exists($menuPath)) {
            throw new Exception("菜单文件不存在：{$menuPath}");
        }
        // 获取菜单数据
        $data = file_get_contents($menuPath);
        // 转换数据格式
        $data = json_decode($data, true);
        // 数据排序
        $data = list_sort_by($data, 'sort', 'asc');
        // 返回数据
        return $data;
    }

    /**
     * 获取处理后菜单数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getMenuData()
    {
        // 获取原始菜单数据
        $data = self::getOriginMenus();
        // 替换菜单数据
        $data = self::replaceMenusData($data);
        // 返回菜单数据
        return $data;
    }

    /**
     * 替换菜单数据
     * @param array $data
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function replaceMenusData(array $data)
    {
        foreach ($data as $key => $value) {
            // 替换菜单访问应用名称
            if (request()->xBaseName) {
                // 访问项目名称
                $xBaseName = request()->xBaseName;
                // 替换路径地址
                $path = str_replace('__PROJECT_NAME__', $xBaseName, $value['path']);
                $value['path'] = $path;
                // 替换菜单参数
                $auth_params = str_replace('__PROJECT_NAME__', $xBaseName, $value['auth_params']);
                $value['auth_params'] = $auth_params;
                // 替换组件参数
                $component = str_replace('__PROJECT_NAME__', $xBaseName, $value['component']);
                $value['component'] = $component;
            }
            // 替换菜单应用名称
            if (request()->appName) {
                // 应用名称
                $appName = request()->appName;
                // 替换路径地址
                $path = str_replace('__APP_NAME__', $appName, $value['path']);
                $value['path'] = $path;
                // 替换菜单参数
                $auth_params = str_replace('__APP_NAME__', $appName, $value['auth_params']);
                $value['auth_params'] = $auth_params;
                // 替换组件参数
                $component = str_replace('__APP_NAME__', $appName, $value['component']);
                $value['component'] = $component;
            }
            // 递归子级
            if (isset($value['children']) && !empty($value['children'])) {
                $value['children'] = self::replaceMenusData($value['children']);
            }
            $data[$key] = $value;
        }
        return $data;
    }

    /**
     * 解析菜单数据
     * @param array $menus
     * @param array $data
     * @return array
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     * @email 416716328@qq.com
     */
    public static function parseMenus(array $menus, array $data = [])
    {
        foreach ($menus as $value) {
            // 设置临时处理数据
            $menuData = $value;
            // 设置图标
            $menuData['icon'] = isset($value['icon']) ? $value['icon'] : '';
            // 菜单组件
            $menuData['component'] = isset($value['component']) ? $value['component'] : 'none/index';
            // 设置默认附带参数
            $menuData['auth_params'] = isset($value['auth_params']) ? $value['auth_params'] : '';
            // 设置是否系统菜单
            $menuData['is_system'] = isset($value['is_system']) ? $value['is_system'] : '10';
            // 是否默认菜单
            $menuData['is_default'] = isset($value['is_default']) ? $value['is_default'] : '10';
            // 菜单是否显示
            $menuData['show'] = isset($value['show']) ? $value['show'] : '10';
            // 删除子级菜单
            if (isset($menuData['children'])) {
                unset($menuData['children']);
            }
            // 设置菜单数据
            $data[] = $menuData;
            // 递归处理子级菜单
            if (isset($value['children']) && !empty($value['children'])) {
                $data = array_merge($data, self::parseMenus($value['children']));
            }
        }
        return $data;
    }
}
