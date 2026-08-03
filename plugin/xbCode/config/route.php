<?php
use Webman\Route;
use plugin\xbCode\utils\FrameUtil;

// 禁用默认路由
Route::disableDefaultRoute();

// 注册默认首页路由
$homeRoute = FrameUtil::getHomeRoute();
Route::get('/', $homeRoute['home'])->middleware($homeRoute['middlewares']);

// 注册总后台
$module = FrameUtil::getBackend();
Route::get("/{$module}", [
    \plugin\xbCode\app\admin\controller\IndexController::class,
    'index',
]);
Route::group("/{$module}", function () {
    Route::get('/', [
        \plugin\xbCode\app\admin\controller\IndexController::class,
        'index',
    ]);
    Route::get('/Index/site', [
        \plugin\xbCode\app\admin\controller\IndexController::class,
        'site',
    ]);
    // 总后台静态资源
    Route::get("/assets/{file:.+}", function ($file) {
        $path = dirname(__DIR__) . "/public/backend/assets/{$file}";
        return response()->file($path);
    });
});

// 注册通用普通配置项
Route::any('/app/{plugin}/admin/Config/{name}', [
    \plugin\xbCode\app\admin\controller\ConfigController::class,
    'config',
]);
// 注册通用选项卡配置
Route::any('/app/{plugin}/admin/Tabs/{name}', [
    \plugin\xbCode\app\admin\controller\ConfigController::class,
    'tabs',
]);

// 安装路由
$module = "install";
Route::get("/{$module}", [
    \plugin\xbCode\app\install\controller\IndexController::class,
    'index',
]);
Route::group("/{$module}", function () {
    Route::get('/', [
        \plugin\xbCode\app\install\controller\IndexController::class,
        'index',
    ]);
    Route::get("/assets/{file:.+}", function ($file) {
        $path = dirname(__DIR__) . "/public/install/assets/{$file}";
        return response()->file($path);
    });
});

// 插件预览图
Route::get('/app/{name}/preview.svg', function ($name) {
    $path = base_path() . "/plugin/{$name}/preview.svg";
    if (!file_exists($path)) {
        throw new \Exception('插件预览图不存在');
    }
    // 输出图片
    return response()->withHeader('content-type', 'image/svg+xml')->file($path);
});