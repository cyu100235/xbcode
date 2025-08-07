<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\event;

use plugin\xbCode\api\Menus;
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
        AdminRule::where('plugin', $name)->save(['state'=> $value]);
        // 获取该插件顶级菜单
        $menus = config("plugin.{$name}.menu",[]);
        foreach ($menus as $val) {
            $where = [
                'path' => $val['path'],
                'plugin' => $val['plugin'] ?? 'xbCode'
            ];
            AdminRule::where($where)->save(['state'=> $value]);
        }
    }
}
