<?php
namespace xbcode\providers;

use support\Cache;
use app\model\AdminRule;
use xbcode\providers\menus\WebMenuTrait;
use xbcode\providers\menus\ConvertTrait;
use xbcode\providers\menus\CascaderTrait;
use xbcode\providers\menus\MenuDataTrait;
use xbcode\providers\menus\AdminMenuTrait;
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
    // 总后台菜单
    use AdminMenuTrait;
    // 站点菜单
    use WebMenuTrait;
    
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
    public static function defaultMenus(array $where = [])
    {
        $where = array_merge([
            ['is_default', '=', '20'],
        ], $where);
        $data = MenuProvider::getMenus($where)->column('path');
        return $data;
    }

    /**
     * 获取父级规则
     * @param int $pid 父级ID
     * @param mixed $rules 规则
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getParentRules(int $pid, $rules = [])
    {
        $model = AdminRule::where('id', $pid)->find();
        if (!$model) {
            return $rules;
        }
        $rules[] = $model['path'];
        if ($model['pid'] != 0) {
            return self::getParentRules($model['pid'], $rules);
        } else {
            return $rules;
        }
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