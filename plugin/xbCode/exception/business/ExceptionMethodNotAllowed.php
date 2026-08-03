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
 * 405 Method Not Allowed 请求方法不允许
 *
 * 使用场景：
 * - 接口不支持当前 HTTP 方法（如：对只读接口执行 POST/DELETE）
 * - RESTful 资源仅允许特定方法访问
 * - 跨域预检请求未正确处理
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ExceptionMethodNotAllowed extends ExceptionBase
{
    /**
     * HTTP 状态码
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $code = 405;
}
