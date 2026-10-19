<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

use plugin\xbCode\app\model\AdminRole;

/**
 * 后台布局接口
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Layouts
{
    /**
     * 默认布局配置
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function defaults(): array
    {
        return [
            // 布局选中：default默认布局，sideBar侧边双栏，user用户中心
            'active' => 'sideBar',
            // 可选布局模式取自枚举，长度大于 0 时前端开放「主题设置」里的布局切换
            'layout' => ['default', 'sideBar', 'user'],
            // 主题模式：dark深色，light浅色，OS跟随系统
            'mode' => 'light',
            // 主题色，留空使用内置主题色
            'colors' => '',
            // 侧边栏颜色，留空使用内置配色（支持纯色和渐变色）
            'sideColor' => "linear-gradient(135deg, #151b26 0%, #6178aa 100%)",
            // 是否折叠菜单
            'collapse' => false,
            // 布局配置，根据布局模式动态加载
            'layouts' => [
                // ------------------------------ 默认布局 ------------------------------
                'default' => [
                    // 主题类型：light 浅色 | dark 深色 | OS 跟随系统
                    'theme' => 'OS',
                    // 主题色，留空使用内置主题色
                    'primaryColor' => '',
                    // 侧边栏颜色，留空使用内置配色
                    'sideColor' => '',
                    // 主题自定义样式，留空使用内置主题
                    'themeCss' => '',
                    // 侧边菜单是否默认折叠
                    'isCollapse' => false,
                    // 底部高度
                    'footerHeight' => 40,
                    // 顶部高度
                    'headerHeight' => 60,
                    // 图标尺寸
                    'logoSize' => 40,
                    // 侧边栏展开宽度
                    'sideMenuOrdinaryWidth' => 220,
                    // 侧边栏折叠宽度
                    'sideMenuCollapseWidth' => 64,
                ],
                // ------------------------------ 侧边双栏 ------------------------------
                'sideBar' => [
                    // 主题类型：light 浅色 | dark 深色 | OS 跟随系统
                    'theme' => 'OS',
                    // 主题色，留空使用内置主题色
                    'primaryColor' => '',
                    // 侧边栏颜色，留空使用内置配色
                    'sideColor' => '',
                    // 主题自定义样式，留空使用内置主题
                    'themeCss' => '',
                    // 侧边菜单是否默认折叠
                    'isCollapse' => false,
                    // 底部高度
                    'footerHeight' => 40,
                    // 顶部高度
                    'headerHeight' => 60,
                    // 图标尺寸
                    'logoSize' => 40,
                    // 侧边栏展开宽度
                    'sideMenuOrdinaryWidth' => 280,
                    // 侧边栏折叠宽度
                    'sideMenuCollapseWidth' => 80,
                ],
                // ------------------------------ 用户中心 ------------------------------
                'user' => [
                    // 主题类型：light 浅色 | dark 深色 | OS 跟随系统
                    'theme' => 'OS',
                    // 主题色，留空使用内置主题色
                    'primaryColor' => '',
                    // 侧边栏颜色，留空使用内置配色
                    'sideColor' => '',
                    // 主题自定义样式，留空使用内置主题
                    'themeCss' => '',
                    // 侧边菜单是否默认折叠
                    'isCollapse' => false,
                    // 底部高度
                    'footerHeight' => 40,
                    // 顶部高度
                    'headerHeight' => 60,
                    // 图标尺寸
                    'logoSize' => 40,
                    // 侧边栏展开宽度
                    'sideMenuOrdinaryWidth' => 220,
                    // 侧边栏折叠宽度
                    'sideMenuCollapseWidth' => 64,
                ],
            ],
        ];
    }

    /**
     * 布局模式选项
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function layoutOptions(): array
    {
        return [
            ['label' => '默认布局', 'value' => 'default'],
            ['label' => '侧边双栏', 'value' => 'sideBar'],
            ['label' => '用户中心', 'value' => 'user'],
        ];
    }

    /**
     * 主题模式选项
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function modeOptions(): array
    {
        return [
            ['label' => '浅色', 'value' => 'light'],
            ['label' => '深色', 'value' => 'dark'],
            ['label' => '跟随系统', 'value' => 'OS'],
        ];
    }

    /**
     * 内置主题颜色预设
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function colorPresets(): array
    {
        return ['#2468f2', '#ff6a00', '#0bc286', '#13c2c2', '#722ed1', '#f23d3d'];
    }

    /**
     * 内置左侧菜单颜色预设
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function sideColorPresets(): array
    {
        return ['#ffffff', '#151b26', '#2f3542', '#ff6a00', '#2468f2', '#0bc286', '#13c2c2', '#722ed1', '#f23d3d'];
    }

    /**
     * 获取角色主题布局参数
     * @param int $roleId 角色ID
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function role(int $roleId): array
    {
        if (empty($roleId)) {
            return static::defaults();
        }
        $theme = AdminRole::where('id', $roleId)->value('theme');
        if (is_string($theme)) {
            $theme = json_decode($theme, true) ?: [];
        }
        return static::config(is_array($theme) ? $theme : []);
    }

    /**
     * 合并角色主题配置到默认布局
     * @param array $theme 角色主题配置
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function config(array $theme = []): array
    {
        $data = static::defaults();
        // 可选布局模式：传入后覆盖默认值，置为空数组表示前端不开放布局切换
        if (array_key_exists('layout', $theme)) {
            $data['layout'] = array_values((array) $theme['layout']);
        }
        // 当前布局模式
        $active = $theme['active'] ?? $data['active'];
        // 未开放布局切换时以已存在的布局为准，否则必须命中可选项，非法则回退默认
        $isValid = empty($data['layout'])
            ? isset($data['layouts'][$active])
            : in_array($active, $data['layout']);
        if (!$isValid) {
            $active = $data['active'];
        }
        $data['active'] = $active;
        // 主题模式
        if (!empty($theme['mode'])) {
            $data['mode'] = $theme['mode'];
        }
        // 主题色
        if (array_key_exists('colors', $theme)) {
            $data['colors'] = (string) $theme['colors'];
        }
        // 侧边栏颜色
        if (array_key_exists('sideColor', $theme)) {
            $data['sideColor'] = (string) $theme['sideColor'];
        }
        // 是否折叠菜单
        if (array_key_exists('collapse', $theme)) {
            $data['collapse'] = (bool) $theme['collapse'];
        }
        // 同步主题参数到当前布局配置
        $data['layouts'][$active]['theme'] = $data['mode'];
        $data['layouts'][$active]['primaryColor'] = $data['colors'];
        $data['layouts'][$active]['sideColor'] = $data['sideColor'];
        $data['layouts'][$active]['isCollapse'] = $data['collapse'];
        return $data;
    }
}