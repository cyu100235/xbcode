<?php

namespace app\admin\controller;

use app\admin\view\PluginConfigView;
use app\common\service\action\PluginUpdateAction;
use app\common\service\CloudSerivce;
use app\common\XbController;
use Webman\Event\Event;
use support\Request;

/**
 * 插件操作
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PluginsActionController extends XbController
{
    /**
     * 导入插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function import(Request $request)
    {
        $file = $request->file('file');
        Event::dispatch('admin.event.PluginImportEvent.import', $file);
        return $this->success('插件导入成功');
    }

    /**
     * 导出插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:导出插件未完成
    public function export(Request $request)
    {
        $file = $request->file('file');
        Event::dispatch('admin.event.PluginImportEvent.import', $file);
        return $this->success('导出插件成功');
    }

    /**
     * 安装插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function install(Request $request)
    {
        $data         = $request->post();
        $data['step'] = $request->post('step', 'depend');
        $result       = Event::dispatch('admin.event.PluginInstallEvent.start', $data);
        $data         = current($result);
        return $data;
    }

    /**
     * 更新插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:更新插件未完成
    public function update(Request $request)
    {
        return PluginUpdateAction::start($request);
    }

    /**
     * 卸载插件
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function uninstall(Request $request)
    {
        $data         = $request->post();
        $data['step'] = $request->post('step', 'database');
        $result       = Event::dispatch('admin.event.PluginUnInstallEvent.start', $data);
        $data         = current($result);
        return $data;
    }

    /**
     * 购买订单
     * @param \support\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:购买订单未完成
    public function order(Request $request)
    {
        return CloudSerivce::create($request);
    }

    /**
     * 统一下订单
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:统一下订单未完成
    public function unifiedOrder(Request $request)
    {
        return CloudSerivce::unifiedOrder($request);
    }

    /**
     * 插件详情
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:插件详情未完成
    public function detail(Request $request)
    {
        $name    = $request->get('name', '');
        $version = $request->get('version', '');
        $data    = CloudSerivce::pluginDetail($name, $version);
        return $this->successRes($data);
    }

    /**
     * 插件配置
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(Request $request)
    {
        if ($request->method() === 'PUT') {
            $group  = $request->get('name', '');
            $post   = $request->post();
            $active = $post['active'];
            unset($post['active']);
            $data = [
                'group' => "{$group}_{$active}",
                'data' => $post
            ];
            Event::dispatch('admin.event.SettingsEvent.config', $data);
            // 返回结果
            return $this->success('保存成功');
        }
        $builder = PluginConfigView::config();
        $builder->setMethod('PUT');
        $data = $builder->create();
        return $this->successRes($data);
    }

    /**
     * 插件演示
     * @param \support\Request $request
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    # TODO:插件演示未完成
    public function demo(Request $request)
    {
        $data = [];
        return $this->successRes($data);
    }
}
