<?php

use Webman\Route;

// 图标和模板路由展示
Route::get('/app/xbDeveloper/{type}/{file}', function ($type, $file) {
    $file = base_path("plugin/xbDeveloper/data/preview/{$type}/{$file}");
    if (!file_exists($file)) {
        return response('File not found', 404);
    }
    return response(file_get_contents($file), 200)->withHeaders([
        'Content-Type' => 'image/svg+xml'
    ]);
});
