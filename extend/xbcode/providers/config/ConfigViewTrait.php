<?php

namespace xbcode\providers\config;

use xbcode\builder\FormBuilder;
use Exception;

/**
 * 配置表单视图虚类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait ConfigViewTrait
{
    /**
     * 获取配置表单
     * @param string|null $name
     * @return \xbcode\builder\FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
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
     * @return \xbcode\builder\FormBuilder
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getFormView(array $data): FormBuilder
    {
        $builder = new FormBuilder;
        $builder->setMethod('PUT');
        foreach ($data as $value) {
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
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getConfigTemplate(string $path): array
    {
        // 获取模板文件
        $filePath = "/settings/{$path}.php";
        // 完整模板文件
        $fullPath = base_path() . $filePath;
        if (!file_exists($fullPath)) {
            throw new Exception("{$filePath} - 配置模板文件不存在");
        }
        // 获取插件配置
        $config = include $fullPath;
        if (empty($config)) {
            throw new Exception("配置模板数据获取失败");
        }
        return $config;
    }
}