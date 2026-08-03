<?php
return [
    // 系统配置选项卡标签，填写插件目录setting/tab/文件名
    'setting' => 'system',
    'workbench' => [
        [
            'sort' => -999,
            'name' => 'XbAdminWorkbench',
        ],
    ],
    // 全局组件
    'components' => [
        [
            'name' => 'XbAdminToolbar',
            'component' => file_get_contents(dirname(__DIR__) . '/app/admin/view/index/toolbarView.vue')
        ],
        [
            'name' => 'XbAdminWorkbench',
            'component' => file_get_contents(dirname(__DIR__) . '/app/admin/view/index/workbenchView.vue')
        ],
    ],
];