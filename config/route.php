<?php

use Webman\Route;

// 禁用默认路由
Route::disableDefaultRoute();

// 注册引导路由
Route::get('/', [\plugin\xbCode\app\index\controller\IndexController::class, 'index']);

// 注册总后台
$module = "backend";
Route::group("/{$module}", function () {
    Route::get('/', [\plugin\xbCode\app\admin\controller\IndexController::class, 'index']);
    Route::get('/Index/site', [\plugin\xbCode\app\admin\controller\IndexController::class, 'site']);
});
// 总后台静态资源
Route::get("/{$module}/assets/{file:.+}", function ($file) {
    $path = dirname(__DIR__) . "/public/backend/assets/{$file}";
    return response()->file($path);
});
// 安装路由
$module = "install";
Route::get("/{$module}", [\plugin\xbCode\app\install\controller\IndexController::class, 'index']);
Route::group("/{$module}", function () {
    Route::get('/', [\plugin\xbCode\app\install\controller\IndexController::class, 'index']);
    Route::get("/assets/{file:.+}", function ($file) {
        $path = dirname(__DIR__) . "/public/install/assets/{$file}";
        return response()->file($path);
    });
});