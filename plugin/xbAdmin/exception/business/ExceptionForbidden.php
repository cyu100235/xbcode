<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\exception\business;

/**
 * 403 无操作权限
 *
 * 使用场景：
 * - 已登录但角色未分配该路由权限
 * - 账号被禁用
 *
 * 注意：这里刻意不继承 ExceptionUnauthorized，
 * 否则会带出 EVENT:KICKOUT 事件，导致越权访问被误判为登录态失效而踢出登录。
 */
class ExceptionForbidden extends ExceptionBase
{
    /**
     * 业务状态码
     * @var int
     */
    protected $code = 403;

    /**
     * 消息内容
     * @var string
     */
    protected $message = '你没有权限访问';

    /**
     * 前端事件名称
     * @var string
     */
    protected $eventName = 'EVENT:NOTIFY';
}
