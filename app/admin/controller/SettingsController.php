<?php
namespace app\admin\controller;

use app\common\providers\ConfigFormProvider;
use app\common\providers\ConfigProvider;
use hg\apidoc\annotation as Apidoc;
use app\common\XbController;
use Webman\Event\Event;
use support\Request;

/**
 * 系统设置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SettingsController extends XbController
{
    /**
     * 配置表单
     * @Apidoc\Method ("GET,PUT")
     * @param \support\Request $request
     * @param string $group
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(Request $request, string $plugin = '', string $group = '')
    {
        $group  = $request->get('group', $group);
        // 分组参数为空时，则为系统配置
        if (empty($group)) {
            $group = $plugin;
            $plugin = '';
        }
        $plugin = $request->get('plugin', $plugin);
        if ($request->method() === 'PUT') {
            $post = $request->post();
            $data = [
                'group' => $group,
                'data'  => $post,
            ];
            Event::dispatch('common.event.SettingsEvent.config', $data);
            // 返回结果
            return $this->success('保存成功');
        }
        if (empty($group)) {
            return $this->fail('分组参数错误');
        }
        $data     = ConfigProvider::get($group, '', [], ['parse' => false]);
        $builder  = ConfigFormProvider::formView($plugin, $group);
        $builder  = $builder->setFormData($data);
        $formView = $builder->create();
        return $this->successRes($formView);
    }

    /**
     * 选中配置表单
     * @Apidoc\Method ("GET,PUT")
     * @param \support\Request $request
     * @param string $group
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function selected(Request $request, string $plugin = '', string $group = '')
    {
        $plugin = $request->get('plugin', $plugin);
        $group  = $request->get('group', $group);
        if ($request->method() === 'PUT') {
            $post = $request->post();
            $data = [
                'group' => $group,
                'data'  => $post,
            ];
            Event::dispatch('common.event.SettingsEvent.config', $data);
            // 返回结果
            return $this->success('保存成功');
        }
        if (empty($group)) {
            return $this->fail('分组参数错误');
        }
        $data    = ConfigProvider::get($group, '', [], ['parse' => false]);
        $builder = ConfigFormProvider::formView($plugin, $group);
        $builder->setFormData($data);
        $formView = $builder->create();
        return $this->successRes($formView);
    }
}
