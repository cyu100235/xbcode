<?php

return [
    [
        "plugin" => "xbCode",
        // 插件标题
        "title" => "插件市场",
        // 插件地址
        "path" => "Plugins",
        // 请求方式，可以是数组或字符串，字符串示例: GET,POST,PUT,DELETE
        "method" => "GET",
        // 菜单类型：10目录，20菜单，30:按钮
        "type" => "10",
        // 菜单图标
        "icon" => "AppstoreOutlined",
        // 菜单参数，组件类型是远程组件时，该处写组件接口地址
        "params" => "",
        // 是否显示：10隐藏，20显示
        "is_show" => 20,
        // 菜单排序，默认值越小越靠前
        "sort" => 1,
        // 父级菜单地址，不填写或不存在时为顶级菜单
        "pid" => "",
        // 继续添加子菜单
        "children" => [
            [
                // 以下参数如上，pid自动获取当前父级菜单地址
                "title" => "插件管理",
                "path" => "admin/Index/index",
                "method" => "GET",
                "type" => "20",
                "icon" => "",
                "params" => "app/xbPlugins/admin/Index/index",
                "is_show" => 20,
                "sort" => 10,
                "children" => [
                    [
                        "title" => "获取插件列表",
                        "path" => "admin/Index/plugins",
                        "method" => "GET",
                        "type" => "30",
                        "icon" => "",
                        "params" => "",
                        "is_show" => 10,
                        "children" => [
                        ],
                    ],
                    [
                        "title" => "导入插件",
                        "path" => "admin/Index/import",
                        "method" => "GET,POST",
                        "type" => "30",
                        "icon" => "",
                        "params" => "",
                        "is_show" => 10,
                        "children" => [
                        ],
                    ],
                    [
                        "title" => "安装插件",
                        "path" => "admin/Index/install",
                        "method" => "GET,POST",
                        "type" => "30",
                        "icon" => "",
                        "params" => "",
                        "is_show" => 10,
                        "children" => [
                        ],
                    ],
                    [
                        "title" => "更新插件",
                        "path" => "admin/Index/update",
                        "method" => "GET,PUT",
                        "type" => "30",
                        "icon" => "",
                        "params" => "",
                        "is_show" => 10,
                        "children" => [
                        ],
                    ],
                    [
                        "title" => "卸载插件",
                        "path" => "admin/Index/uninstall",
                        "method" => "GET,DELETE",
                        "type" => "30",
                        "icon" => "",
                        "params" => "",
                        "is_show" => 10,
                        "children" => [
                        ],
                    ],
                ],
            ],
        ],
    ],
];
