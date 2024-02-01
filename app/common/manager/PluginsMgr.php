<?php

namespace app\common\manager;

use app\common\service\CloudService;
use think\Request;

trait PluginsMgr
{
    /**
     * 插件列表
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function index(Request $request)
    {
        return CloudService::getPluginList();
    }

    /**
     * 插件详情
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function detail(Request $request)
    {
        $pluginName = $request->get('app_name', '');
        return CloudService::getPluginDetail($pluginName);
    }

    /**
     * 购买插件
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function buy(Request $request)
    {
        $pluginName = $request->get('app_name', '');
        return CloudService::buyPlugin($pluginName);
    }

    /**
     * 安装插件
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        return $this->successRes([]);
    }

    /**
     * 更新插件
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function update(Request $request)
    {
        return $this->successRes([]);
    }

    /**
     * 卸载插件
     * @param \think\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstall(Request $request)
    {
        return $this->successRes([]);
    }
}
