<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 *
 * 中间件按「模块名 => 中间件类名列表」的二级结构注册，
 * 框架最终生效范围为「本插件 + 本模块」，不会影响其他插件的同名模块。
 *
 * 注意：不能写成 ['\xxx\Middleware::class'] 这种一级列表，
 * 否则框架会抛出 RuntimeException: Bad middleware config。
 * 也不要使用 '@' 键，那会让中间件变成全局中间件，作用于整个站点的每个请求。
 */

return [
    // admin 模块（后台权限管理）统一登录与权限校验
    '' => [
        \plugin\xbAdmin\app\middleware\AuthMiddleware::class,
    ],
];
