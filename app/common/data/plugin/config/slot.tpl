<?php

// 插件插槽配置
return [
    // 接管后台欢迎页面
    'admin.index' => function(){
        // 业务逻辑...
        // 渲染vue组件字符串
        return xbView(dirname(__DIR__) . '/public/vue/welcome');
    }
];