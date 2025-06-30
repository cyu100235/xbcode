<?php

namespace plugin\xbCode\api;

use Exception;
use plugin\xbCode\builder\Builder;
use plugin\xbCode\builder\Components\Tabs;
use plugin\xbCode\builder\Renders\Form;

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
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function formView(string $path, string $type = '')
    {
        switch ($type) {
            // 选项卡表单
            case 'tabs':
                $builder = static::getTabsBuilder($path);
                break;
            // 侧边栏表单
            case 'sidebar':
                $builder = static::getSidebarBuilder($path);
                break;
            // 普通表单
            default:
                $builder = static::getConfigBuilder($path);
                break;
        }
        // 返回表单句柄
        return $builder;
    }

    /**
     * 获取选项卡表单视图
     * @param string $path
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function getTabsBuilder(string $path)
    {
        // 获取配置模板数据
        $data = static::getConfigTemplate($path, 'tabs');
        return Builder::form(function (Form $builder) use ($data) {
            $tabContent = [];
            foreach ($data as $item) {
                if (!isset($item['label'])) {
                    throw new Exception('选项卡标题参数错误');
                }
                if (!isset($item['children'])) {
                    throw new Exception('选项卡子配置错误');
                }
                /** @var Tabs */
                $tabs = Tabs::make();
                $children = [];
                foreach ($item['children'] as $value) {
                    if (!isset($value['field'])) {
                        throw new Exception('字段参数错误');
                    }
                    if (!isset($value['type'])) {
                        throw new Exception('组件类型参数错误');
                    }
                    $children[] = $builder->addRow(
                        $value['type'] ?? 'InputText',
                        $value['field'] ?? '',
                        $value['title'] ?? '',
                        $value['value'] ?? '',
                        $value['extra'] ?? []
                    );
                }
                $tabContent[] = [
                    'title' => $item['label'],
                    'body' => $children,
                ];
            }
            $builder->addLayout([
                $tabs->tabs($tabContent)
            ]);
        });
    }

    /**
     * 获取侧边栏表单渲染器
     * @param string $path
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function getSidebarBuilder(string $path)
    {
        // 获取配置模板数据
        $data = static::getConfigTemplate($path, 'sidebar');
        return Builder::form(function (Form $builder) use ($data) {
            $tabContent = [];
            foreach ($data as $item) {
                if (!isset($item['label'])) {
                    throw new Exception('选项卡标题参数错误');
                }
                if (!isset($item['children'])) {
                    throw new Exception('选项卡子配置错误');
                }
                /** @var Tabs */
                $tabs = Tabs::make();
                $tabs->tabsMode('vertical');
                $children = [];
                foreach ($item['children'] as $value) {
                    if (!isset($value['field'])) {
                        throw new Exception('字段参数错误');
                    }
                    if (!isset($value['type'])) {
                        throw new Exception('组件类型参数错误');
                    }
                    $children[] = $builder->addRow(
                        $value['type'] ?? 'InputText',
                        $value['field'] ?? '',
                        $value['title'] ?? '',
                        $value['value'] ?? '',
                        $value['extra'] ?? []
                    );
                }
                $tabContent[] = [
                    'title' => $item['label'],
                    'body' => $children,
                ];
            }
            $builder->addLayout([
                $tabs->tabs($tabContent),
            ]);
        });
    }

    /**
     * 获取普通表单渲染器
     * @param string $path
     * @throws \Exception
     * @return Form
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function getConfigBuilder(string $path)
    {
        // 获取配置模板数据
        $data = static::getConfigTemplate($path, 'config');
        // 创建表单渲染器
        return Builder::form(function (Form $builder) use ($data) {
            foreach ($data as $value) {
                if (!isset($value['field'])) {
                    throw new Exception('字段参数错误');
                }
                if (!isset($value['type'])) {
                    throw new Exception('组件类型参数错误');
                }
                $builder->addRow(
                    $value['type'] ?? 'text',
                    $value['field'] ?? '',
                    $value['title'] ?? '',
                    $value['value'] ?? '',
                    $value['extra'] ?? []
                );
            }
        });
    }

    /**
     * 获取配置模板数据
     * @param string $path
     * @param string $type
     * @throws \Exception
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getConfigTemplate(string $path, string $type)
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
        $filePath = "/plugin/{$plugin}/setting/{$templatePath}/{$type}.php";
        // 完整模板文件
        $fullPath = base_path() . $filePath;
        if (!file_exists($fullPath)) {
            throw new Exception("{$filePath} - 配置模板文件不存在");
        }
        // 获取插件配置
        $data = include $fullPath;
        // 返回配置数据
        return $data;
    }
}