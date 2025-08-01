<?php
namespace plugin\xbPlugins\api;

use Exception;
use plugin\xbPlugins\app\model\Plugins;

/**
 * 插件状态接口
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsState
{
    /**
     * 插件安装检测
     * @param string $name
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function checked(string $name)
    {
        if (Plugins::where('name', $name)->count()) {
            return true;
        }
        return false;
    }
    
    /**
     * 设置插件状态
     * @param string $name
     * @param string $state
     * @throws \Exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function setState(string $name, string $state)
    {
        // 检测插件是否存在
        $pluginPath = base_path() . "/plugin/{$name}";
        if (!is_dir($pluginPath)) {
            throw new Exception('插件不存在');
        }
        if (!in_array($state, ['10', '20', '30'])) {
            throw new Exception('插件状态参数错误');
        }
        $model = Plugins::where('name', $name)->find();
        if (!$model) {
            throw new Exception('插件未安装');
        }
        $stateText = [
            '10' => '未启用',
            '20' => '已启用',
            '30' => '已禁用',
        ];
        if ($model->state == $state) {
            throw new Exception("插件{$stateText[$state]}");
        }
        if (!$model->save(['state' => $state])) {
            throw new Exception('修改插件状态失败');
        }
    }
}