<?php
namespace plugin\xbPlugins\app\admin\controller;

use support\Request;
use plugin\xbCode\XbController;
use plugin\xbPlugins\api\PluginsApi;
use plugin\xbPlugins\service\xbcode\api\PluginService;

/**
 * 插件市场
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class IndexController extends XbController
{
    /**
     * 插件市场视图
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function index(Request $request)
    {
        return $this->display();
    }

    /**
     * 获取插件列表
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function plugins(Request $request)
    {
        try {
            $type = $request->get('type', '');
            switch ($type) {
                // 插件市场
                case 'plugins':
                    $data = PluginService::getPluginList();
                    break;
                // 本地插件
                case 'local':
                    $data = PluginsApi::getLocalPluginList();
                    break;
                // 已安装插件
                case 'installed':
                    $data = PluginsApi::getInstalledPluginList();
                    break;
            }
            return $this->successRes($data);
        } catch (\Throwable $th) {
            return $this->successRes([]);
        }
    }
}
