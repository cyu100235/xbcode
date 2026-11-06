<?php

return [
    // 系统配置选项卡标签，填写插件目录setting/tab/文件名
    // 'setting' => 'system',
    // 重定向地址
    // 'redirect' => '/backend',
    // 引导路由(安装后访问的默认页，全局仅一个插件生效)
    // 'home' => [
    //     // 控制器
    //     \plugin\{PLUGIN_NAME}\app\home\controller\IndexController::class,
    //     // 控制器方法
    //     'index'
    // ],
    // 工作台组件名称
    // 'workbench' => [
    //     [
    //         'sort' => 0,
    //         'name' => 'XbAdminToolbar',
    //     ],
    // ],
    // 后台待办事项（由 xbCode 工具栏汇总展示），条目结构：
    // [
    //     'title' => '待办名称',
    //     'desc' => '补充说明',
    //     'count' => 数量（为 0 时不展示）,
    //     'icon' => '图标名称',
    //     'path' => '/app/{PLUGIN_NAME}/admin/控制器/方法',
    //     'query' => ['参数名' => '参数值'],
    //     'sort' => 1,
    //     'urgent' => true, // 紧急事项会在登录后台后弹窗提醒
    // ]
    // 也可填闭包或「类::方法」回调，返回条目数组
    // 'todo' => [
    //     \plugin\{PLUGIN_NAME}\api\Todo::class,
    //     'items',
    // ],
    // 工具栏项列表
    // 'toolbars' => [
    //     [
    //         'name' => '首页',
    //         'icon' => 'Monitor',
    //         'url' => '/',
    //         'target' => '_blank',
    //     ],
    // ],
    // 工具栏组件名称
    // 'toolbar' => [
    //     [
    //         'sort' => 0,
    //         'name' => 'XbAdminToolbar',
    //     ],
    // ],
    // 全局组件
    // 'components' => [
    //     [
    //         'name' => 'XbUserToolbar',
    //         'component' => file_get_contents(dirname(__DIR__) . '/view/index/toolbarView.vue')
    //     ],
    //     [
    //         'name' => 'XbUserWorkbench',
    //         'component' => file_get_contents(dirname(__DIR__) . '/view/index/workbenchView.vue')
    //     ],
    // ],
    // 扩展图标库
    // 'icons' => [
    //     [
    //         'name' => 'xbCode',
    //         'url' => '/app/xbCode/icons/iconfont.css',
    //     ],
    // ],
];