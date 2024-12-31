<?php
namespace xbcode\providers\menus;

use xbcode\utils\DataUtil;

/**
 * 菜单数据转换
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ConvertTrait
{
    /**
     * 二维数组转树形结构
     * @param array $menus 二维菜单数组
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function menu2DToTree(array $menus)
    {
        $data = DataUtil::channelLevel($menus, 0, '', 'id', 'pid');
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
}