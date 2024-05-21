<?php

namespace app\common\providers;

use app\common\builder\FormBuilder;
use app\admin\validate\SettingsValidate;
use Exception;

/**
 * 配置表单工具类
 * @author 贵州小白基地网络科技有限公司
 * @copyright (c) 贵州小白基地网络科技有限公司
 */
class ConfigFormProvider
{
    /**
     * 获取配置表单
     * @param string $group
     * @return FormBuilder
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function formView(string|null $group): FormBuilder
    {
        $template = self::getConfigTemplate($group);
        if (empty($template)) {
            throw new Exception("配置模板不能为空");
        }
        $builder = self::getFormView($template);
        return $builder;
    }

    /**
     * 获取选项卡配置表单
     * @param string|null $group
     * @return FormBuilder
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function tabsFormView(string|null $group)
    {
        $template = self::getConfigTemplate($group);
        if (empty($template)) {
            throw new Exception("配置模板不能为空");
        }
        $active = current($template)['field'] ?? '';
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        $builder->initTabsActive('active', $active, [
            'props'             => [
                // 选项卡样式
                'type'          => 'line',
                'tabPosition'   => 'top',
            ],
        ]);
        foreach ($template as $value) {
            // 获取表单
            $formRow = self::getFormView($value['children'] ?? [])
            ->getBuilder()
            ->formRule();
            // 设置表单项
            $builder->addTab(
                $value['field'],
                $value['title'],
                $formRow
            );
        }
        // 结束选项卡
        $builder->endTabs();
        // 获取表单句柄
        return $builder;
    }

    /**
     * 获取表单规则
     * @param array $data
     * @return FormBuilder
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    public static function getFormView(array $data): FormBuilder
    {
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        foreach ($data as $value) {
            // 虚线框不验证
            if (!in_array($value['component'],['NDivider','x-title','XTitle'])) {
                // 数据验证
                xbValidate(SettingsValidate::class, $value);
                // 验证扩展组件
                if (in_array($value['component'], ['checkbox', 'radio', 'select'])) {
                    if (empty($value['extra'])) {
                        throw new Exception("[{$value['title']}] - 扩展【extra】数据不能为空");
                    }
                }
            }
            // 设置默认值
            $configValue = !isset($value['value']) ? '' : $value['value'];
            // 设置扩展数据
            $configExtra = empty($value['extra']) ? [] : $value['extra'];
            // 虚线框
            if ($value['component'] === 'NDivider') {
                $builder->addDivider($value['title'], $configExtra);
                continue;
            }
            // 表单子标题
            if ($value['component'] === 'XTitle') {
                $builder->addTitle($value['title'], $configExtra);
                continue;
            }
            // 普通组件
            $builder->addRow(
                $value['field'] ?? '',
                $value['component'],
                $value['title'],
                $configValue,
                $configExtra
            );
        }
        // 获取表单句柄
        return $builder;
    }

    /**
     * 获取配置模板
     * @param string $group
     * @return mixed
     * @author 贵州小白基地网络科技有限公司
     * @copyright 贵州小白基地网络科技有限公司
     */
    private static function getConfigTemplate(string|null $group)
    {
        $path = base_path() . "/config/setting/{$group}.php";
        if (!file_exists($path)) {
            throw new Exception("系统配置文件不存在");
        }
        $config = require $path;
        return $config;
    }
}