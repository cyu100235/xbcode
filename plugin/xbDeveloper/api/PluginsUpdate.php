<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbDeveloper\api;

use Exception;
use Webman\Event\Event;
use plugin\xbCode\api\PluginsApi;
use plugin\xbCode\api\PluginsBaseApi;
use plugin\xbCode\app\model\Plugins;

/**
 * 插件更新接口类
 * 复用 xbCode 插件脚本执行与插件记录能力
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class PluginsUpdate extends PluginsBaseApi
{
    /**
     * 更新步骤
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $steps = [
        [
            'title' => '更新数据',
            'name' => 'update',
            'next' => 'finish',
        ],
        [
            'title' => '更新完成',
            'name' => 'finish',
            'next' => 'success',
        ],
    ];

    /**
     * 执行更新数据
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function update()
    {
        $this->script();
        // 触发事件
        Event::dispatch('xbCode.Plugins.Update.update', [
            'name' => $this->name,
            'version' => $this->version
        ]);
        return $this->nextResult('更新数据完成...');
    }

    /**
     * 更新完成
     * @throws Exception
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function finish()
    {
        // 获取插件信息
        $plugin = PluginsApi::make()->get($this->name);
        if (empty($plugin)) {
            throw new Exception('插件信息不存在');
        }
        // 保留插件原启用状态
        $state = Plugins::where('name', $this->name)->value('state');
        // 更新插件记录
        PluginsApi::make()->install($plugin['name'], $plugin, $state === '20' ? '20' : '10');
        // 触发事件
        Event::dispatch('xbCode.Plugins.Update.finish', $plugin);
        return $this->nextResult("名称：{$plugin['title']}，标识：{$plugin['name']}，版本：{$plugin['version']} - 更新完成...");
    }
}
