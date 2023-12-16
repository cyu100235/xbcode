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
     * 获取菜单最新ID
     * @param array $menus
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
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
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function save(array $data,int $id = null,string $xBaseName = null)
    {
        if (empty($data)) {
            throw new Exception('菜单数据不能为空');
        }
        # 处理父级菜单
        if (!empty($data['pid']) && is_array($data['pid'])) {
            $data['pid'] = end($data['pid']);
        }
        # 路由地址首字母转大写
        $data['path']   = ucfirst($data['path']);
        # 处理图标
        if (isset($data['icon']['icon'])) {
            $data['icon']   = $data['icon']['icon'];
        }
        # 获取菜单数据
        $menus = self::getMenus($xBaseName,true);
        if ($id) {
            # 修改菜单数据
            $arrayIndex = array_search($id, array_column($menus, 'id'));
            $data['id']         = $id;
            $menus[$arrayIndex] = $data;
        } else {
            # 新增菜单数据
            $id = self::getMenuId($menus);
            $id++;
            $data['id'] = $id;
            array_push($menus, $data);
        }
        # 保存菜单数据
        self::saveMenusData($menus,$xBaseName);
        return $id;
    }

    /**
     * 批量保存菜单数据
     * @param array $data
     * @param string $xBaseName
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function saveAll(array $data,string $xBaseName = null)
    {
        foreach ($data as $value) {
            self::save($value,null,$xBaseName);
        }
    }

    /**
     * 保存菜单数据
     * @param array $menus
     * @param string $xBaseName
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function saveMenusData(array $menus,string $xBaseName = null)
    {
        # 还原菜单格式数据
        $menus = DataUtil::channelLevel($menus, 0, '', 'id', 'pid');
        # 递归处理菜单
        $menus = self::checkData($menus);
        # 转换菜单JSON格式数据
        $menus = json_encode($menus, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        # 获取菜单保存路径
        $menuPath = self::getMenuPath($xBaseName);
        # 储存菜单数据
        file_put_contents($menuPath, $menus);
    }

    /**
     * 删除菜单数据
     * @param int $id
     * @param mixed $xBaseName
     * @return void
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function del(int $id,$xBaseName = null)
    {
        if (empty($id)) {
            throw new Exception('参数错误');
        }
        # 获取菜单数据
        $menus = self::getMenus($xBaseName,true);
        $arrayIndex = array_search($id, array_column($menus, 'id'));
        $detail     = isset($menus[$arrayIndex]) ? $menus[$arrayIndex] : [];
        if (empty($detail)) {
            throw new Exception('菜单数据不存在');
        }
        # 删除元数据
        unset($menus[$arrayIndex]);
        # 保存菜单数据
        self::saveMenusData($menus,$xBaseName);
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
    public static function getMenus(string $xBaseName = null,bool $origin = false)
    {
        # 获得菜单数据
        $data = self::getOriginMenus($xBaseName);
        # 处理菜单数据
        $data = self::parseMenus($data);
        # 处理可展示数据
        foreach ($data as $key => $value) {
            if (!$origin) {
                $value['show'] = $value['show'] == '20' ? '1' : '0';
                $value['is_system'] = $value['is_system'] == '20' ? '1' : '0';
                $value['component'] = $value['component'] == 'none/index' ? '' : $value['component'];
                if (is_array($value['method'])) {
                    $value['method'] = current($value['method']);
                }
            }
            $data[$key] = $value;
        }
        # 返回数据
        return $data;
    }

    /**
     * 获取菜单详情
     * @param int $id
     * @param string $xBaseName
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function find(int $id,string $xBaseName = null)
    {
        $data       = self::getMenus($xBaseName,true);
        $arrayIndex = array_search($id, array_column($data, 'id'));
        $detail     = $data[$arrayIndex] ?? [];
        if (empty($detail)) {
            throw new Exception('菜单数据不存在');
        }
        if (is_string($detail['icon']) && !empty($detail['icon'])) {
            $detail['icon'] = [
                'icon' => $detail['icon'],
            ];
        }
        return $detail;
    }

    /**
     * 获取菜单组件选项
     * @param string $xBaseName
     * @return array
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function getCascaderOptions(string $xBaseName = null)
    {
        $data   = self::getMenus($xBaseName,true);
        $data   = list_sort_by($data, 'sort', 'asc');
        $data   = DataUtil::channelLevel($data, 0, '', 'id', 'pid');
        $data   = self::getChildrenOptions($data);
        $data   = array_merge([
            [
                'label' => '顶级权限菜单',
                'value' => 0
            ]
        ], $data);
        return $data;
    }

    /**
     * 递归拼接cascader组件数据
     * @Author 贵州猿创科技有限公司
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
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public static function getMenuPath(string $xBaseName = null)
    {
        $request  = request();
        $menuPath = app_path() . 'menus.json';
        if ($xBaseName) {
            $menuPath = "{$request->xbBasePath}/{$xBaseName}/menus.json";
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
    public static function getOriginMenus(string $xBaseName = null)
    {
        $menuPath = self::getMenuPath($xBaseName);
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
}
