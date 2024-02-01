<?php

namespace app\common\manager;

use app\common\utils\SettingFormUtil;
use app\common\utils\SettingUtil;
use think\Request;

/**
 * 配置页面管理
 * @author 贵州小白基地网络科技有限公司
 * @copyright (c) 贵州小白基地网络科技有限公司
 */
trait SettingsMgr
{
    /**
     * 配置表单
     * @param \think\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function config(Request $request)
    {
        $group = $request->get('group','');
        if ($request->isPut()) {
            $post  = $request->post();
            if (empty($group)) {
                return $this->fail('分组参数错误');
            }
            // 保存配置项
            SettingUtil::save($group, $post);
            // 返回结果
            return $this->success('保存成功');
        }
        if (empty($group)) {
            return $this->fail('分组参数错误');
        }
        $data     = SettingUtil::getOriginal($group,[]);
        $formView = SettingFormUtil::formView($group);
        $formView = $formView->setFormData($data)->create();
        return $this->successRes($formView);
    }

    /**
     * 分割配置表单
     * @param \think\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function divider(Request $request)
    {
        $group = $request->get('group');
        if ($request->isPut()) {
            $post  = $request->post();
            // 保存配置项
            SettingUtil::save($group, $post);
            // 返回结果
            return $this->success('保存成功');
        }
        if (empty($group)) {
            return $this->fail('分组参数错误');
        }
        $data     = SettingUtil::getOriginal($group,[]);
        $formView = SettingFormUtil::getDivider($group);
        $formView = $formView->setFormData($data)->create();
        return $this->successRes($formView);
    }

    /**
     * 条件显示配置项
     * @param \think\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function selected(Request $request)
    {
        $group  = $request->get('group');
        if ($request->isPut()) {
            $post            = $request->post();
            // 保存配置项
            SettingUtil::save($group, $post);
            // 返回结果
            return $this->success('保存成功');
        }
        if (empty($group)) {
            return $this->fail('分组参数错误');
        }
        $data = SettingUtil::getOriginal($group,[]);
        $builder = SettingFormUtil::formView($group);
        $builder->setFormData($data);
        $formView = $builder->create();
        return $this->successRes($formView);
    }

    /**
     * 选项卡配置表单
     * @param \think\Request $request
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public function tabs(Request $request)
    {
        $group = $request->get('group');
        if ($request->isPut()) {
            $post   = $request->post();
            // 保存配置项
            SettingUtil::save($group, $post);
            // 返回结果
            return $this->success('保存成功');
        }
        if (empty($group)) {
            return $this->fail('分组参数错误');
        }
        $data = SettingUtil::getOriginal($group,[]);
        $builder = SettingFormUtil::tabsFormView($group);
        $builder->setFormData($data);
        $formView = $builder->create();
        return $this->successRes($formView);
    }
}
