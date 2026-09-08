<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 *
 * 插件异常处理器，键为模块名，空键 '' 表示插件默认处理器。
 * 框架解析规则：config("plugin.$plugin.exception")[$app] ?? 配置[''] ?? 全局处理器。
 *
 * 本插件的处理器负责把登录失效、无权限、业务异常等统一转换为
 * 积木云前端可识别的 JSON 结构（code/message/data），并携带通知事件。
 */

return [
    '' => \plugin\xbAdmin\exception\Handler::class,
];
