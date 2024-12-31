<?php
namespace xbcode\providers;

use support\Cache;
use app\model\AdminRule;
use xbcode\providers\menus\ConvertTrait;
use xbcode\providers\menus\CascaderTrait;
use xbcode\providers\menus\MenuDataTrait;
use xbcode\providers\menus\MenuActionTrait;

/**
 * 菜单服务提供者
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MenuProvider
{
    // 菜单操作
    use MenuActionTrait;
    // 菜单级联
    use CascaderTrait;
    // 菜单数据转换
    use ConvertTrait;
    // 菜单数据处理
    use MenuDataTrait;
    
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
     * 获取默认菜单数据
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function defaultMenus()
    {
        $data = MenuProvider::getMenus(['is_default' => '20'])->column('path');
        return $data;
    }

    /**
     * 设置菜单缓存
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function cacheMenus()
    {
        $data = AdminRule::order('sort asc')->select()->toArray();
        Cache::set('menus_data', $data, 3600);
    }
}