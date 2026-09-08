<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\exception\business;

/**
 * 通用业务失败异常
 *
 * 用于「登录账号错误」「数据不存在」等只需弹提示、不需要跳转的场景，
 * 前端收到 EVENT:NOTIFY 后弹出消息并保持当前页面。
 */
class ExceptionBusiness extends ExceptionBase
{
    /**
     * 业务状态码
     * @var int
     */
    protected $code = 400;

    /**
     * 消息内容
     * @var string
     */
    protected $message = '操作失败';

    /**
     * 前端事件名称
     * @var string
     */
    protected $eventName = 'EVENT:NOTIFY';
}
