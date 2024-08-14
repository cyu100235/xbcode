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
     * @param string|null $plugin
     * @param string|null $group
     * @throws \Exception
     * @return \app\common\builder\FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function formView(string|null $plugin, string|null $group): FormBuilder
    {
        $template = self::getConfigTemplate($plugin, $group);
        if (empty($template)) {
            throw new Exception("配置模板不能为空");
        }
        $type = array_column($template, 'type');
        // 选项卡表单
        if (in_array('tabs', $type)) {
            return self::tabsView($plugin, $group, $template);
        }
        // 普通表单
        $builder = self::getFormView($template);
        return $builder;
    }

    /**
     * 获取选项卡配置表单
     * @param string $plugin
     * @param string $group
     * @param array $template
     * @throws \Exception
     * @return FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function tabsView(string $plugin, string $group, array $template)
    {
        $active  = current($template)['field'] ?? '';
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        $builder->initTabsActive('active', $active, [
            'props' => [
                // 选项卡样式
                'type'        => 'line',
                'tabPosition' => 'top',
            ],
        ]);
        // 遍历选项卡
        foreach ($template as $value) {
            if (empty($value['title'])) {
                throw new Exception("选项卡标题不能为空");
            }
            if (empty($value['field'])) {
                throw new Exception("选项卡字段不能为空");
            }
            if (empty($value['type']) || $value['type'] !== 'tabs') {
                throw new Exception("选项卡 {$value['title']}下的表单项不能为空");
            }
            // 获取选项卡字符串插件配置
            if (is_string($value['children'])) {
                // 获取配置分组标识
                $name = $value['children'];
                // 获取插件配置
                $children = config("plugin.{$plugin}.settings.{$name}", []);
                // 获取表单
                $value['children'] = $children;
            }
            if (!is_array($value['children'])) {
                throw new Exception("选项卡【{$value['title']}】下的表单项必须是数组");
            }
            // 选项卡下的表单项不能为空
            if (empty($value['children'])) {
                throw new Exception("选项卡【{$value['title']}】下的表单项不能为空");
            }
            // 获取表单
            $formRow = self::getFormView($value['children'] ?? [])
                ->getBuilder()
                ->formRule();
            // 设置表单项
            $builder->addTab(
                $value['field'] ?? '',
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
     * @throws \Exception
     * @return \app\common\builder\FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getFormView(array $data): FormBuilder
    {
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        foreach ($data as $value) {
            // 虚线框不验证
            if (!in_array($value['component'], ['NDivider', 'x-title', 'XTitle'])) {
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
    private static function getConfigTemplate(string|null $plugin, string|null $group)
    {
        if (empty($plugin)) {
            $plugin = request()->get('plugin');
        }
        if (empty($plugin)) {
            $plugin = request()->plugin;
        }
        if (empty($plugin) && strrpos(request()->path(), '/app/') !== false) {
            $plugin = explode('/', request()->path())[2] ?? '';
        }
        if ($plugin) {
            // 获取插件配置
            $config = config("plugin.{$plugin}.settings.{$group}", []);
            // 设置插件名称
            request()->plugin = $plugin;
        } else {
            // 获取系统配置
            $path   = base_path("config/setting/{$group}.php");
            $config = include $path;
        }
        if (empty($config)) {
            throw new Exception("配置模板数据获取失败");
        }
        return $config;
    }
}