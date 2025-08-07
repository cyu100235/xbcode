<?php
namespace plugin\xbCode\api;

use Exception;
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
        $ids = [$id];
        $children = AdminRule::where('pid', $id)->where('state', '20')->column('id');
        foreach ($children as $child) {
            $ids = array_merge($ids, self::getChildrenIds($child));
        }
        return $ids;
    }

    /**
     * 验证菜单数据
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function validateMenus(array $data)
    {
        foreach ($data as $value) {
            if (empty($value['title'])) {
                throw new Exception('菜单标题不能为空');
            }
            if (empty($value['path'])) {
                throw new Exception('菜单路径不能为空');
            }
            if (empty($value['is_show'])) {
                throw new Exception('菜单是否显示不能为空');
            }
            if (!isset($value['type'])) {
                throw new Exception('菜单类型不能为空');
            }
            if (!empty($value['children']) && is_array($value['children'])) {
                self::validateMenus($value['children']);
            }
        }
    }
}