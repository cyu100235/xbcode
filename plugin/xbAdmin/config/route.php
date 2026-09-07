<?php
use Webman\Route;

// 注册后台路由
$module = 'admin';
Route::get("/{$module}", [
    \plugin\xbAdmin\app\admin\controller\IndexController::class,
    'index',
]);
Route::group("/{$module}", function () {
    Route::get('/', [
        \plugin\xbAdmin\app\admin\controller\IndexController::class,
        'index',
    ]);
    Route::get('/Index/site', [
        \plugin\xbAdmin\app\admin\controller\IndexController::class,
        'site',
    ]);
});
// 总后台静态资源
Route::get("/backend/assets/{file:.+}", function ($file) {
    // 单页应用产物的资源引用固定为 /backend/assets/ 开头，此处映射到本插件 public 目录
    if (str_starts_with($file, "/") || str_contains($file, "..") || str_contains($file, "\\")) {
        return response("403 forbidden", 403);
    }
    $path = dirname(__DIR__) . "/public/backend/assets/{$file}";
    if (!is_file($path)) {
        return response("404 Not Found", 404);
    }
    return response()->file($path);
});