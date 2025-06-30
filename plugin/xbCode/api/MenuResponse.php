<?php
namespace plugin\xbCode\api;

use Exception;
use plugin\xbCode\app\model\AdminRule;

/**
 * 资源菜单操作
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class MenuResponse
{    
    /**
     * 添加资源菜单
     * @param array $parent
     * @param array $data
     * @throws \Exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function addResponse(array $parent, array $data)
    {
        // 检测父级菜单地址
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
        if (empty($data)) {
            throw new Exception('资源菜单数据不能为空');
        }
        $pid = $parent['id'] ?? '';
        if (empty($pid)) {
            $pidModel = AdminRule::where('path', $parent['path'])->where('state', '20')->find();
            if (!$pidModel) {
                throw new Exception('父级菜单不存在');
            }
            $pid = $pidModel['id'];
        }
        $response = array_column(self::responseOption(), null, 'value');
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
            $menu['path'] = "{$module}/{$controller}/{$res['value']}";
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
     * 获取资源菜单选项
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function responseOption()
    {
        return [
            [
                'label' => '添加',
                'value' => 'add',
                'disabled' => false,
            ],
            [
                'label' => '修改',
                'value' => 'edit',
                'disabled' => false,
            ],
            [
                'label' => '删除',
                'value' => 'del',
                'disabled' => false,
            ],
            [
                'label' => '修改列',
                'value' => 'rowEdit',
                'disabled' => false,
            ],
            [
                'label' => '表格',
                'value' => 'Table',
                'disabled' => false,
            ],
        ];
    }
}