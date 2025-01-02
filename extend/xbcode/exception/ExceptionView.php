<?php
namespace xbcode\exception;

use Webman\Http\Request;
use Webman\Http\Response;
use support\view\ThinkPHP;

/**
 * 渲染异常模板视图
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ExceptionView
{
    /**
     * 抛出模板异常
     * @param \Webman\Http\Request $request
     * @param \Throwable $exception
     * @return \Webman\Http\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function render(Request $request, \Throwable $exception): Response
    {
        // 异常模板路径
        $templatePath = '/extend/xbcode/exception/view';
        // 渲染异常模板视图
        $vars = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'project' => config('projects'),
        ];
        // 抛出异常响应
        return new Response(200, [], ThinkPHP::render($templatePath, $vars));
    }
}