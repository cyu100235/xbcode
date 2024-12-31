<?php
namespace xbcode\providers\menus;

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
    public static function getMenus(array $where = [], array $field = [])
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
     * @param array $data 菜单数据，pid为父级菜单的路径
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function create(array $data)
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
     * @param array $data 菜单数据，pid为父级菜单的路径
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function createMenus(array $data)
    {
        foreach ($data as $item) {
            self::create($item);
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
        if (empty($parentPathFull[1]) || empty($parentPathFull[2])) {
            throw new Exception('父级菜单路由格式错误');
        }
        $module = $parentPathFull[0] ?? '';
        $controller = $parentPathFull[1] ?? '';
        $action = $parentPathFull[2] ?? '';
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
                'is_show' => '10',
                'is_default' => '10',
                'is_system' => '10',
                'type' => '30',
                'icon' => '',
                'sort' => '0',
                'params' => '',
            ];
            $menu['title'] = "{$parent['title']}-{$res['label']}";
            $menu['component'] = $res['component'];
            $menu['path'] = "{$module}/{$controller}/{$res['value']}";
            $menu['method'] = $res['methods'];
            // 是否插件路由
            if (!empty($parent['plugin'])) {
                $menu['plugin'] = $parent['plugin'];
            }
            // 检测是否表格
            if ($res['value'] === 'Table') {
                $menu['path'] = "{$module}/{$controller}/{$action}{$res['value']}";
            }
            $model = new AdminRule;
            if (!$model->save($menu)) {
                throw new Exception("生成资源菜单失败");
            }
        }
    }
    
    /**
     * 修改菜单
     * @param string $path
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function edit(string $path, array $data)
    {
        $model = self::findMenu($path);
        if (!$model) {
            throw new Exception('菜单不存在');
        }
        if (!$model->save($data)) {
            throw new Exception('修改菜单失败');
        }
    }
    
    /**
     * 删除菜单
     * @param string $path
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function delete(string $path)
    {
        $model = self::findMenu($path);
        if (!$model) {
            return true;
        }
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
    public static function delMenus(array $paths)
    {
        foreach ($paths as $path) {
            self::delete($path);
        }
    }
    
    /**
     * 查找菜单
     * @param string $key
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function findMenu(string $path)
    {
        $where = [
            'path' => $path,
        ];
        $model = AdminRule::where($where)->find();
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
                'label' => '修改列',
                'value' => 'rowEdit',
                'disabled' => false,
                'methods' => 'GET,PUT',
                'component' => 'none/index',
            ],
            [
                'label' => '表格',
                'value' => 'Table',
                'disabled' => false,
                'methods' => 'GET',
                'component' => 'none/index',
            ],
        ];
    }
}