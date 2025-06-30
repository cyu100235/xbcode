<?php
namespace plugin\xbCode\api;

use Exception;
use plugin\xbCode\app\model\Plugins;
use plugin\xbCode\utils\DirUtil;

/**
 * 插件卸载接口类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsUninstallApi extends PluginsBaseApi
{
    /**
     * 安装步骤
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $steps = [
        [
            'title' => '卸载数据',
            'name' => 'unInstall',
            'next' => 'finish',
        ],
        [
            'title' => '卸载完成',
            'name' => 'finish',
            'next' => 'success',
        ],
    ];

    /**
     * 执行卸载数据
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function uninstall()
    {
        $this->script();
        return $this->nextResult('卸载数据完成...');
    }

    /**
     * 卸载完成
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected function finish()
    {
        // 卸载记录
        $model = Plugins::where('name', $this->name)->find();
        if ($model) {
            $model->delete();
        }
        return $this->nextResult('插件卸载完成...');
    }
}