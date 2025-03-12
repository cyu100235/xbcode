<?php

namespace plugin\xbConfig\api;

use Exception;
use plugin\xbCode\builder\FormBuilder;

/**
 * 配置视图接口类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ConfigView
{
    /**
     * 获取配置表单视图
     * @param string $path
     * @throws \Exception
     * @return FormBuilder
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function formView(string $path): FormBuilder
    {
        $template = self::getConfigTemplate($path);
        if (empty($template)) {
            throw new Exception("配置模板不能为空");
        }
        // 效验配置模板
        $builder = self::getFormView($template);
        // 返回表单句柄
        return $builder;
    }
    
    /**
     * 获取表单规则
     * @param array $data
     * @return FormBuilder
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getFormView(array $data): FormBuilder
    {
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        foreach ($data as $value) {
            if (!isset($value['field'])) {
                throw new Exception('字段参数错误');
            }
            if (!isset($value['type'])) {
                throw new Exception('组件类型参数错误');
            }
            // 设置表单方向参数
            if ($value['field'] === 'config' && $value['type'] === 'config') {
                $formWidth = $value['extra']['width'] ?? '100%';
                $formPosition = $value['extra']['position'] ?? 'center';
                $builder->setFormWidth($formWidth, $formPosition);
                continue;
            }
            if (!isset($value['title'])) {
                throw new Exception('标题参数错误');
            }
            // 虚线框不验证
            if (!in_array($value['type'], ['xbTitle'])) {
                $components = ['radio', 'checkbox', 'select'];
                if (!in_array($value['type'], $components) && $value['field'] !== 'active') {
                    // 验证扩展组件
                    if (in_array($value['type'], ['checkbox', 'radio', 'select'])) {
                        if (empty($value['extra'])) {
                            throw new Exception("[{$value['title']}] - 扩展【extra】数据不能为空");
                        }
                    }
                }
            }
            // 设置默认值
            $configValue = $value['value'] ?? '';
            // 检测是否选项值
            if (!in_array($value['type'], ['checkbox', 'radio', 'select'])) {
                $configValue = !isset($value['value']) ? '' : $value['value'];
            }
            // 设置扩展数据
            $configExtra = empty($value['extra']) ? [] : $value['extra'];
            // 虚线框
            if ($value['type'] === 'NDivider') {
                $builder->addDivider($value['title'], $configExtra);
                continue;
            }
            // 表单子标题
            if ($value['type'] === 'xbTitle') {
                $builder->addTitle($value['title'], $configExtra);
                continue;
            }
            // 普通组件
            $builder->addRow(
                $value['field'] ?? '',
                $value['type'],
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
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getConfigTemplate(string $path): array
    {
        $paths = explode('/', $path);
        if (empty($paths[0])) {
            throw new Exception('获取配置模板失败，插件名称参数错误');
        }
        // 获取插件名称
        $plugin = $paths[0];
        unset($paths[0]);
        // 获取配置模板路径
        $templatePath = implode('/', $paths);
        // 获取模板文件
        $filePath = "/plugin/{$plugin}/setting/{$templatePath}.php";
        // 完整模板文件
        $fullPath = base_path() . $filePath;
        if (!file_exists($fullPath)) {
            throw new Exception("{$filePath} - 配置模板文件不存在");
        }
        // 获取插件配置
        $data = include $fullPath;
        if (empty($data)) {
            return [];
        }
        // 替换参数
        $namePath = str_replace('/', '.', $templatePath);
        // 获取配置数据
        $data = array_map(function ($item)use($namePath) {
            if ($item['field'] === 'xbValidate') {
                return $item;
            }
            // 替换字段名称
            $item['field'] = "{$namePath}.{$item['field']}";
            // 返回配置
            return $item;
        }, $data);
        // 返回配置模板
        return $data;
    }
}