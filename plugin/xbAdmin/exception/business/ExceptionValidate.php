<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\exception\business;

/**
 * 400 参数校验失败
 *
 * 由 plugin\xbAdmin\app\validate 下的验证器抛出，
 * 消息内容为第一条校验错误，前端直接弹出提示。
 */
class ExceptionValidate extends ExceptionBase
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
    protected $message = '提交的数据校验失败';

    /**
     * 前端事件名称
     * @var string
     */
    protected $eventName = 'EVENT:NOTIFY';
}
