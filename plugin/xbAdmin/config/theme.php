<?php
return [
    // 布局选中：default默认布局，sideBar侧边双栏，user用户中心
    'active' => 'default',
    // 可选布局模式取自枚举，长度大于 0 时前端开放「主题设置」里的布局切换
    'layout' => ['default', 'sideBar', 'user'],
    // 主题模式：dark深色，light浅色，OS跟随系统
    'mode' => 'OS',
    // 主题色，留空使用内置主题色
    'colors' => '',
    // 侧边栏颜色，留空使用内置配色
    'sideColor' => '',
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
            'sideMenuOrdinaryWidth' => 200,
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
            'sideMenuOrdinaryWidth' => 200,
            // 侧边栏折叠宽度
            'sideMenuCollapseWidth' => 64,
        ],
    ],
];