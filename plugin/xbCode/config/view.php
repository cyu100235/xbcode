<?php

use support\view\Raw;
use support\view\Twig;
use support\view\Blade;
use support\view\ThinkPHP;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;

return [
    'handler' => Blade::class,
    // 修复：illuminate/view 在渲染时通过 Illuminate\Container\Container::getInstance()
    // 解析 blade.compiler，而 jenssegers/blade 只在构造时设置了 Facade 应用容器，
    // 未将其注册为全局容器实例，导致报错 Target class [blade.compiler] does not exist
    'extension' => function ($blade) {
        $container = Facade::getFacadeApplication();
        if ($container) {
            Container::setInstance($container);
        }
    },
];
