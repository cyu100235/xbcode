<?php

return [
    'index.index' => function () {
        return view('index/index');
    },
    'admin.index' => function () {
        $viewPath = 'public/vue/admin/welcome.vue';
        $path = base_path($viewPath);
        $content = file_get_contents($path);
        return $content;
    }
];