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
     * @param array $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function createMenu(array $data)
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
        $model = AdminRule::where('path', $data['path'])->find();
        if ($model) {
            return $model->id;
        }
        // 请求类型全转大写
        $data['methods'] = strtoupper($data['methods']);
        // 获取父级PID
        $pid = 0;
        if (isset($data['pid']) && $data['pid']) {
            $model = AdminRule::where('path', $data['pid'])->find();
            if (!$model) {
                throw new Exception('父级菜单不存在');
            }
            $pid = $model->id;
        }
        $data['pid'] = $pid;
        $model = new AdminRule;
        if (!$model->save($data)) {
            throw new Exception('创建菜单失败');
        }
        return $model->id;
    }

    /**
     * 批量创建菜单
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function createMenus(array $data)
    {
        foreach ($data as $item) {
            self::createMenu($item);
        }
    }

    /**
     * 创建资源菜单
     * @param array $parent 父级菜单数据
     * @param array $data 资源菜单数据
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function createResponse(array $parent, array $data)
    {
        if (empty($parent['path'])) {
            throw new Exception('父级菜单路由不能为空');
        }
        $parentPathFull = explode('/', $parent['path']);
        if (empty($parentPathFull[0])) {
            throw new Exception('父级菜单路由格式错误');
        }
        $parentPath = $parentPathFull[0];
        if (empty($parent['component'])) {
            throw new Exception('父级菜单组件不能为空');
        }
        if ($parent['component'] !== 'table/index') {
            throw new Exception('父级菜单组件必须是表格组件');
        }
        if (empty($data)) {
            throw new Exception('资源菜单数据不能为空');
        }
        $pid = $parent['id'] ?? '';
        if (empty($pid)) {
            $pidModel = self::findMenu($parent['path']);
            if (!$pidModel) {
                throw new Exception('父级菜单不存在');
            }
            $pid = $pidModel['id'];
        }
        $response = array_column(self::resourcesOption(), null, 'value');
        foreach ($data as $method) {
            if (!isset($response[$method])) {
                continue;
            }
            // 获取资源
            $res = $response[$method];
            // 拼接数据
            $menu = [
                'pid' => $pid,
                'plugin_name' => $parent['plugin_name'],
                'module_name' => $parent['module_name'],
                'is_show' => '10',
                'is_default' => '10',
                'is_system' => '10',
                'icon' => '',
                'sort' => '0',
                'params' => '',
            ];
            $menu['title'] = "{$parent['title']}-{$res['label']}";
            $menu['component'] = $res['component'];
            $menu['path'] = "{$parentPath}/{$res['value']}";
            $menu['methods'] = $res['methods'];
            $model = new AdminRule;
            if (!$model->save($menu)) {
                throw new Exception("生成资源菜单失败");
            }
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

    /**
     * 获取资源选项
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function resourcesOption()
    {
        return [
            [
                'label' => '添加',
                'value' => 'add',
                'disabled' => false,
                'methods' => 'GET,POST',
                'component' => 'form/index',
            ],
            [
                'label' => '修改',
                'value' => 'edit',
                'disabled' => false,
                'methods' => 'GET,PUT',
                'component' => 'form/index',
            ],
            [
                'label' => '删除',
                'value' => 'del',
                'disabled' => false,
                'methods' => 'GET,DELETE',
                'component' => 'none/index',
            ],
            [
                'label' => '表格',
                'value' => 'Table',
                'disabled' => false,
                'methods' => 'GET',
                'component' => 'none/index',
            ],
            [
                'label' => '修改列',
                'value' => 'rowEdit',
                'disabled' => false,
                'methods' => 'GET,PUT',
                'component' => 'none/index',
            ],
        ];
    }
}