<?php
namespace app\common\providers\menus;

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
    public static function menuList(array $where = [], array $field = [])
    {
        $data = self::getMenus($where, $field)->select()->toArray();
        return $data;
    }

    /**
     * 创建菜单
     * @param string|int $parent
     * @param array $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function createMenu(string|int $parent, array $data)
    {
        if (empty($data['title'])) {
            throw new Exception('菜单名称不能为空');
        }
        if (empty($data['path'])) {
            throw new Exception('菜单路径不能为空');
        }
        if (empty($data['component'])) {
            throw new Exception('菜单组件不能为空');
        }
        if (empty($data['methods'])) {
            throw new Exception('请求类型不能为空');
        }
        // 检测菜单是否已存在
        $model = self::findMenu($data['path']);
        if ($model) {
            return $model->id;
        }
        // 请求类型全转大写
        $data['methods'] = strtoupper($data['methods']);
        // 组装父级PID
        $pid = 0;
        if ($parent && is_string($parent)) {
            $model = AdminRule::where('path', $parent)->find();
            if (!$model) {
                throw new Exception('父级菜单不存在');
            }
            $pid = $model->id;
        }
        if (is_int($parent)) {
            $pid = $parent;
        }
        $data['pid'] = $pid;
        $model       = new AdminRule;
        if (!$model->save($data)) {
            throw new Exception('创建菜单失败');
        }
        return $model->id;
    }

    /**
     * 批量创建菜单
     * @param int|string $parent
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function createMenus(int|string $parent, array $data)
    {
        foreach ($data as $item) {
            self::createMenu($parent, $item);
        }
    }

    /**
     * 修改菜单
     * @param int|string $key
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function editMenu(int|string $key, array $data)
    {
        $model = self::findMenu($key);
        if (!$model->save($data)) {
            throw new Exception('修改菜单失败');
        }
    }

    /**
     * 删除菜单
     * @param int|string $key
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function delMenu(int|string $key)
    {
        $model = self::findMenu($key);
        // 检测是否有子级菜单
        $count = AdminRule::where('pid', $model->id)->count();
        if ($count) {
            return true;
        }
        if (!$model->delete()) {
            throw new Exception('删除菜单失败');
        }
        return true;
    }

    /**
     * 批量删除菜单
     * @param array $keys
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function delMenus(array $keys)
    {
        foreach ($keys as $path) {
            self::delMenu($path);
        }
    }

    /**
     * 查找菜单
     * @param int|string $key
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function findMenu(int|string $key)
    {
        $where = [];
        if (is_int($key)) {
            $where['id'] = $key;
        }
        if (is_string($key)) {
            $where['path'] = $key;
        }
        $model = AdminRule::where($where)->find();
        if (!$model) {
            throw new Exception('菜单不存在');
        }
        return $model;
    }
}