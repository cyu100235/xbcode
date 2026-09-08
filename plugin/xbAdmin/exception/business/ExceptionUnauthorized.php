<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\exception\business;

/**
 * 401 未登录/登录态失效
 *
 * 使用场景：
 * - 未携带令牌、令牌签名错误或已过期
 * - 令牌中的管理员标识缺失
 */
class ExceptionUnauthorized extends ExceptionBase
{
    /**
     * 业务状态码
     * @var int
     */
    protected $code = 401;

    /**
     * 消息内容
     * @var string
     */
    protected $message = '你没有登录，请先登录';

    /**
     * 前端事件名称
     * @var string
     */
    protected $eventName = 'EVENT:KICKOUT';
}
