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
 * 403 Forbidden 禁止访问
 *
 * 使用场景：
 * - 已登录用户但无操作权限（如：普通用户访问管理员接口）
 * - 账号被禁用、IP 被拉黑、角色权限不足
 * - 资源访问被拒绝（如：无权查看他人数据）
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ExceptionForbidden extends ExceptionUnauthorized
{
    /**
     * HTTP 状态码
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $code = 403;

    /**
     * 消息内容
     * @var string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $message = '你没有权限访问';
}
