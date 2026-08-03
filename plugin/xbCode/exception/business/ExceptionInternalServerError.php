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
 * 500 Internal Server Error 服务器内部错误
 *
 * 使用场景：
 * - 代码运行时未捕获的异常、数据库连接失败
 * - 依赖服务调用超时、内部逻辑出现不可预期错误
 * - 通常用于需要对外隐藏具体错误细节的场景
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ExceptionInternalServerError extends ExceptionBase
{
    /**
     * 业务状态码
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $code = 500;
}