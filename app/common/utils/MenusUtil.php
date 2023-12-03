<?php

namespace app\common\utils;
use Exception;

/**
 * 菜单工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MenusUtil
{
    /**
     * 获取二维菜单数据
     * @param string $plugin
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getMenus(string $plugin = null)
    {
        # 获得菜单数据
        $data = self::getMenuData($plugin);
        # 处理菜单数据
        $data = self::parseMenus($data);
        # 处理可展示数据
        foreach ($data as $key => $value) {
            $value['show'] = $value['show'] == '20' ? '1' : '0';
            $value['is_api'] = $value['is_api'] == '20' ? '1' : '0';
            $value['is_system'] = $value['is_system'] == '20' ? '1' : '0';
            $value['component'] = $value['component'] == 'none/index' ? '' : $value['component'];
            if (is_array($value['method'])) {
                $value['method'] = current($value['method']);
            }
            $data[$key] = $value;
        }
        # 返回数据
        return $data;
    }

    /**
     * 获取菜单原始数据
     * @param string $plugin
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getOriginMenus(string $plugin = null)
    {
        $data = self::getMenuData($plugin);
        return $data;
    }

    /**
     * 解析菜单数据
     * @param array $menus
     * @param array $data
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     * @email 416716328@qq.com
     */
    public static function parseMenus(array $menus, array $data = [])
    {
        foreach ($menus as $value) {
            # 设置临时处理数据
            $menuData = $value;
            # 设置图标
            $menuData['icon'] = isset($value['icon']) ? $value['icon'] : '';
            # 菜单组件
            $menuData['component'] = isset($value['component']) ? $value['component'] : 'none/index';
            # 设置默认附带参数
            $menuData['auth_params'] = isset($value['auth_params']) ? $value['auth_params'] : '';
            # 设置是否系统菜单
            $menuData['is_system'] = isset($value['is_system']) ? $value['is_system'] : '10';
            # 是否默认菜单
            $menuData['is_default'] = isset($value['is_default']) ? $value['is_default'] : '10';
            # 菜单是否显示
            $menuData['show'] = isset($value['show']) ? $value['show'] : '10';
            # 是否接口
            $menuData['is_api'] = isset($value['is_api']) ? $value['is_api'] : '10';
            # 删除子级菜单
            if (isset($menuData['children'])) {
                unset($menuData['children']);
            }
            # 设置菜单数据
            $data[] = $menuData;
            # 递归处理子级菜单
            if (isset($value['children']) && !empty($value['children'])) {
                $data = array_merge($data, self::parseMenus($value['children']));
            }
        }
        return $data;
    }

    /**
     * 获取菜单数据
     * @param string $XbaseName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function getMenuData(string $xBaseName = null)
    {
        $request  = request();
        $menuPath = app_path() . 'menus.json';
        if ($xBaseName) {
            $menuPath = "{$request->xbBasePath}/{$xBaseName}/menus.json";
        }
        if (!file_exists($menuPath)) {
            throw new Exception("菜单文件不存在：{$menuPath}");
        }
        $data = file_get_contents($menuPath);
        # 转换数据格式
        $data = json_decode($data, true);
        # 数据排序
        $data = list_sort_by($data,'sort','asc');
        # 返回数据
        return $data;
    }
}
