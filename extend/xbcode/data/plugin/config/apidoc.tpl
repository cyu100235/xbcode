<?php

return [
    'apps' => [
        [
            // （必须）标题
            'title' => '官网端',
            // （必须）控制器目录地址
            'path'  => 'plugin\{PLUGIN_NAME}\app\controller',
            // （必须）唯一的key
            'key'   => 'home',
        ],
    ],
    'docs' => [
        [
            'title' => '测试文档',
            'path'  => 'plugin/{PLUGIN_NAME}/docs/test',
        ],
    ],
];