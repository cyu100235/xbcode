<?php

return [
    'index.index' => function () {
        return view('index/index');
    },
    'admin.index' => function () {
        $content = xbView('public/vue/admin/welcome');
        return $content;
    }
];