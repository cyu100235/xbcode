<?php
namespace plugin\xbCode\api;

use plugin\xbCode\app\model\AdminRule;

/**
 * 菜单数据接口
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MenuData
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
}