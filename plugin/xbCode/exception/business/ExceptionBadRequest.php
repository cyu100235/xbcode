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
 * 400 Bad Request 请求参数错误
 *
 * 使用场景：
 * - 请求参数格式不正确、类型不匹配或缺少必填字段
 * - 表单校验失败、JSON 解析错误、查询参数非法
 * - 业务前置条件不满足（如：日期范围不合法、参数组合冲突）
 *
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class ExceptionBadRequest extends ExceptionBase
{
    /**
     * HTTP 状态码
     * @var int
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected $code = 400;
}
