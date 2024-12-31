<?php
namespace app\backend\controller;

use xbcode\providers\ConfigProvider;
use xbcode\XbController;

/**
 * 系统配置控制器
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SettingController extends XbController
{
    /**
     * 配置项
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(string $path)
    {
        if (request()->method() === 'PUT') {
            $post = request()->post();
            // 保存配置
            ConfigProvider::set($path, '', $post);
            // 返回数据
            return $this->success('保存成功');
        }
        $formData = ConfigProvider::get($path, '', [], [
            'template' => true
        ]);
        $builder = ConfigProvider::formView($path);
        $builder->setFormData($formData);
        $builder->setMethod('PUT');
        $data    = $builder->create();
        return $this->successRes($data);
    }
}