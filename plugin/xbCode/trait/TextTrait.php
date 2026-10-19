<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\trait;

use support\Response;

/**
 * 文本返回类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
trait TextTrait
{
    /**
     * 返回文本内容
     * @param string $content 文本内容
     * @param int $status HTTP状态码
     * @param array $headers 响应头
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function textResponse(string $content, int $status = 200, array $headers = [])
    {
        return response((string)$content, $status, [
            'Content-Type' => 'text/plain; charset=utf-8',
            ...$headers,
        ]);
    }

    /**
     * 返回成功文本，固定输出
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function textSuccess()
    {
        return static::textResponse('1');
    }

    /**
     * 返回错误文本，直接输出对应错误码
     * @param int $code 错误码
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function textFail(int $code = 1)
    {
        return static::textResponse((string)$code);
    }
}