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
 * 502 Bad Gateway 网关错误
 *
 * 使用场景：
 * - 网关或代理服务器从上游服务收到无效响应
 * - 下游微服务宕机、响应超时或返回错误格式
 * - Nginx/网关层与后端 PHP-FPM/Workerman 通信异常
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ExceptionBadGateway extends ExceptionBase
{
    /**
     * 业务状态码
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $code = 502;
}