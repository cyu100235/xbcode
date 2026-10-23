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
        // 获取该插件顶级菜单
        $menus = config("plugin.{$name}.menu", []);
        $menus = MenuChecked::menuTreeTo2D($menus);
        $menus = array_map(function ($item) {
            return [
                'plugin' => $item['plugin'] ?? 'xbCode',
                'path' => $item['path'] ?? '',
                'pid' => $item['pid'] ?? 0,
            ];
        }, $menus);
        foreach ($menus as $val) {
            $pluginName = $val['plugin'] ?? 'xbCode';
            if ($val['pid'] !== 0) {
                $pluginName = $name;
            }
            $where = [
                'path' => $val['path'],
                'plugin' => $pluginName
            ];
            AdminRule::where($where)->save(['state' => $value]);
        }
    }
}
