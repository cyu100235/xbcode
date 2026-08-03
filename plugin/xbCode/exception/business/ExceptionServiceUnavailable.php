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
 * 503 Service Unavailable 服务不可用
 *
 * 使用场景：
 * - 系统维护中、服务临时下线
 * - 请求量过大触发限流、熔断器打开
 * - 依赖的核心服务暂时无法提供服务
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ExceptionServiceUnavailable extends ExceptionBase
{
    /**
     * 业务状态码
     * @var int
     * @copyright 州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $code = 503;
}