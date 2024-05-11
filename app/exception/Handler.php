<?php
namespace app\exception;

use Throwable;
use Webman\Exception\ExceptionHandler;
use Webman\Http\Request;
use Webman\Http\Response;

/**
 * 全局异常处理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Handler extends ExceptionHandler
{
    public $dontReport = [
        BusinessException::class,
    ];

    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * 异常处理
     * @param \Webman\Http\Request $request
     * @param \Throwable $exception
     * @return \Webman\Http\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function render(Request $request, Throwable $exception): Response
    {
        // Apidoc异常处理响应
        if ($exception instanceof \hg\apidoc\exception\HttpException) {
            return response(json_encode([
                "code" => $exception->getCode(),
                "message" => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE), $exception->getStatusCode());
        }
        // 其他异常处理响应
        if (($exception instanceof BusinessException) && ($response = $exception->render($request))) {
            return $response;
        }
        // 返回默认异常处理
        return parent::render($request, $exception);
    }

}