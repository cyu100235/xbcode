<?php
namespace plugin\xbConfig\app\admin\controller;

use plugin\xbCode\XbController;
use plugin\xbConfig\api\ConfigApi;
use plugin\xbConfig\api\ConfigView;

/**
 * 系统配置控制器
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SettingController extends XbController
{
    /**
     * 配置项
     * @param \support\Request $request
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function config(string $path)
    {
        if (request()->method() === 'PUT') {
            $post = request()->post();
            // 保存配置
            ConfigApi::set($post);
            // 返回数据
            return $this->success('保存成功');
        }
        $formData = ConfigApi::get($path, [], [
            'layer' => false,
            'replace' => false,
        ]);
        $builder = ConfigView::formView($path);
        $builder->setFormData($formData);
        $builder->setMethod('PUT');
        $data    = $builder->create();
        return $this->successRes($data);
    }
}