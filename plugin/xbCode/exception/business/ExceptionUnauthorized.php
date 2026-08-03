<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\exception\business;

/**
 * 401 Unauthorized 未认证
 *
 * 使用场景：
 * - 用户未登录、Token 缺失或已过期
 * - JWT 签名验证失败、Session 已失效
 * - 接口需要身份认证但请求中未携带有效凭证
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ExceptionUnauthorized extends ExceptionBase
{
    /**
     * HTTP 状态码
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $code = 401;

    /**
     * 消息内容
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $message = '你没有登录，请先登录';

    /**
     * 事件名称
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $eventName = 'EVENT:KICKOUT';
}
