<?php

namespace app\common\manager;

use app\common\model\Settings;
use app\common\utils\SettingFormUtil;
use app\common\utils\SettingUtil;
use think\Request;

/**
 * 配置页面管理
 * @author 贵州猿创科技有限公司
 * @copyright (c) 贵州猿创科技有限公司
 */
trait SettingsMgr
{
    /**
     * 配置表单
     * @param \think\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function config(Request $request)
    {
        $group = $request->get('group');
        if ($request->isPut()) {
            $post  = $request->post();
            $model = Settings::where('name', $group)->find();
            if (!$model) {
                $model       = new Settings;
                $model->name = $group;
            }
            $model->value = $post;
            if (!$model->save()) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $data     = SettingUtil::getOriginConfig(['name'=> $group],[]);
        $formView = SettingFormUtil::formView($group);
        $formView = $formView->setFormData($data)->create();
        return $this->successRes($formView);
    }

    /**
     * 分割配置表单
     * @param \think\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function divider(Request $request)
    {
        $group = $request->get('group');
        if ($request->isPut()) {
            $post  = $request->post();
            $model = Settings::where('name', $group)->find();
            if (!$model) {
                $model       = new Settings;
                $model->name = $group;
            }
            $model->value = $post;
            if (!$model->save()) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $data     = SettingUtil::getOriginConfig(['name'=> $group],[]);
        $formView = SettingFormUtil::getDivider($group);
        $formView = $formView->setFormData($data)->create();
        return $this->successRes($formView);
    }

    /**
     * 条件显示配置项
     * @param \think\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function selected(Request $request)
    {
        $group  = $request->get('group');
        $active = SettingUtil::getActive($group, '');
        $data   = SettingUtil::getOriginConfig(['name' => $group], []);
        if ($request->isPut()) {
            $post            = $request->post();
            $selected_active = $post['selected_active'] ?? '';
            if (isset($post['selected_active'])) {
                unset($post['selected_active']);
            }
            $model = Settings::where('name', $group)->find();
            if (!$model) {
                $model       = new Settings;
                $model->name = $group;
            }
            if ($selected_active) {
                $model->active = $selected_active;
            }
            if (empty($data)) {
                $data = [
                    $selected_active => $post,
                ];
            } else {
                $data[$selected_active] = $post;
            }
            $model->value = $data;
            if (!$model->save()) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        if ($active && $data) {
            $list = [];
            foreach ($data as $value) {
                $list = array_merge($list, $value);
            }
            $data                    = $list;
            $data['selected_active'] = $active;
        }
        $formView = SettingFormUtil::formView($group);
        $formView = $formView->setFormData($data)->create();
        return $this->successRes($formView);
    }

    /**
     * 选项卡配置表单
     * @param \think\Request $request
     * @return mixed
     * @author 贵州猿创科技有限公司
     * @copyright 贵州猿创科技有限公司
     */
    public function tabs(Request $request)
    {
        $group = $request->get('group');
        if ($request->isPut()) {
            $post   = $request->post();
            $active = $post['active'] ?? '';
            if (isset($post['active'])) {
                unset($post['active']);
            }
            $model = Settings::where('name', $group)->find();
            if (!$model) {
                $model       = new Settings;
                $model->name = $group;
            }
            $model->active = $active;
            $model->value  = $post;
            if (!$model->save()) {
                return $this->fail('保存失败');
            }
            return $this->success('保存成功');
        }
        $active         = SettingUtil::getActive($group, '');
        $data           = SettingUtil::getOriginConfig(['name'=> $group],[]);
        $data['active'] = $active;
        $formView       = SettingFormUtil::tabsFormView($group)
            ->setFormData($data)
            ->create();
        return $this->successRes($formView);
    }
}
