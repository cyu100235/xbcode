<?php
use Webman\Route;

// 禁用默认路由
Route::disableDefaultRoute();

// 注册引导路由
$pluginHome = glob(base_path() . '/plugin/*/config/home.php');
$homeConfig = [
    \plugin\xbCode\app\index\controller\IndexController::class,
    'index'
];
if ($pluginHome) {
    $temp = [];
    foreach ($pluginHome as $configPath) {
        $route = include $configPath;
        if (empty($route) || $temp) {
            continue;
        }
        $temp = $route;
        $homeConfig = $route;
    }
}
Route::get('/', $homeConfig);

// 注册总后台
$module = getenv('ADMIN_URL') ?: 'backend';
Route::get("/{$module}", [\plugin\xbCode\app\admin\controller\IndexController::class, 'index']);
Route::group("/{$module}", function () {
    Route::get('/', [\plugin\xbCode\app\admin\controller\IndexController::class, 'index']);
    Route::get('/Index/site', [\plugin\xbCode\app\admin\controller\IndexController::class, 'site']);
    // 总后台静态资源
    Route::get("/assets/{file:.+}", function ($file) {
        $path = dirname(__DIR__) . "/public/backend/assets/{$file}";
        return response()->file($path);
    });
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

// 插件预览图
Route::get('/app/{name}/preview.svg', function ($name) {
    $path = base_path() . "/plugin/{$name}/preview.svg";
    if (!file_exists($path)) {
        throw new \Exception('插件预览图不存在');
    }
    // 输出图片
    return response()->withHeader('content-type', 'image/svg+xml')->file($path);
});