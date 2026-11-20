<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\event;

use plugin\xbCode\api\MenuChecked;
use plugin\xbCode\app\model\AdminRule;

/**
 * 菜单事件
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginStateEvent
{
    /**
     * 修改插件状态
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function state(array $data)
    {
        $name = $data['name'] ?? '';
        $value = $data['value'] ?? '';
        if (empty($name) || !in_array($value, ['10', '20'], true)) {
            return;
        }
        // 获取该插件菜单
        $menus = config("plugin.{$name}.menu", []);
        $menus = MenuChecked::menuTreeTo2D($menus);
        foreach ($menus as $item) {
            $path = $item['path'] ?? '';
            if (empty($path)) {
                continue;
            }
            // 子级菜单直接同步本插件
            if (($item['pid'] ?? 0) !== 0) {
                AdminRule::where([
                    'path' => $path,
                    'plugin' => $name,
                ])->save(['state' => $value]);
                continue;
            }
            // 顶级菜单：安装时未声明归属的顶级菜单统一挂在 xbCode 下，
            // 需按路径查找实际记录
            $plugin = $item['plugin'] ?? 'xbCode';
            $model = AdminRule::where(['path' => $path, 'plugin' => $plugin])->find();
            if (!$model && $plugin !== $name) {
                $model = AdminRule::where(['path' => $path, 'plugin' => $name])->find();
            }
            if (!$model) {
                continue;
            }
            // 顶级菜单可能被多个插件复用（如「系统配置」由 xbCode 创建，
            // 其他插件在其下追加子菜单），切换单个插件状态时不应连带开启/隐藏整个顶级菜单，
            // 仅当该顶级菜单只被当前插件使用时才同步状态
            if ($model['plugin'] !== $name && !static::exclusiveMenu((int) $model['id'], $name)) {
                continue;
            }
            $model->save(['state' => $value]);
        }
    }

    /**
     * 检测顶级菜单是否只被指定插件使用
     * @param int $id 顶级菜单ID
     * @param string $name 插件标识
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function exclusiveMenu(int $id, string $name)
    {
        $plugins = [];
        static::collectChildrenPlugins($id, $plugins);
        if (empty($plugins)) {
            return false;
        }
        foreach ($plugins as $plugin) {
            if ($plugin !== $name) {
                return false;
            }
        }
        return true;
    }

    /**
     * 递归收集子级菜单的归属插件
     * @param int $id 父级菜单ID
     * @param array $plugins 归属插件集合
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function collectChildrenPlugins(int $id, array &$plugins)
    {
        $rows = AdminRule::where('pid', $id)->column('plugin', 'id');
        foreach ($rows as $childId => $plugin) {
            $plugins[] = (string) $plugin;
            static::collectChildrenPlugins((int) $childId, $plugins);
        }
    }
}
