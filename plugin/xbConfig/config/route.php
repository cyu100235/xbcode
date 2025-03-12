<?php

use Webman\Route;

Route::add(['GET', 'PUT'], '/app/xbConfig/admin/Setting/config/{path:.+}', [
    \plugin\xbConfig\app\admin\controller\SettingController::class,
    'config'
]);