<?php
namespace app\providers\menus;

use app\model\AdminRule;
use Exception;

/**
 * 菜单操作类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait MenuActionTrait
{
    /**
     * 获取菜单数据
     * @param array $where
     * @param array $field
     * @return \think\db\Query
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getMenus(array $where = [], array $field = [])
    {
        $data = AdminRule::where($where)->field($field)->order('sort asc');
        return $data;
    }

    /**
     * 获取菜单数据
     * @param array $where
     * @param array $field
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function menuList(array $where = [],array $field = [])
    {
        $data = self::getMenus($where,$field)->select()->toArray();
        return $data;
    }
    
    /**
     * 创建菜单
     * @param string $path
     * @param array $data
     * @throws \Exception
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function createMenu(string $path,array $data)
    {
        $pid   = 0;
        if ($path) {
            $model = AdminRule::where('path', $path)->find();
            if (!$model) {
                throw new Exception('父级菜单不存在');
            }
            $pid = $model->id;
        }
        $data['pid'] = $pid;
        $model       = new AdminRule;
        if (!$model->save($data)) {
            throw new Exception('创建菜单失败');
        }
        return $model->id;
    }
    
    /**
     * 修改菜单
     * @param int|string $key
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function editMenu(int|string $key,array $data)
    {
    }
    
    /**
     * 删除菜单
     * @param int|string $key
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function delMenu(int|string $key)
    {
    }
}