<?php
namespace plugin\xbCode\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\Exception\ExceptionHandler;
use \hg\apidoc\exception\HttpException;
use plugin\xbCode\utils\trait\JsonTrait;
use support\exception\BusinessException;

/**
 * 全局异常处理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Handler extends ExceptionHandler
{
    // 引入JsonUtil
    use JsonTrait;

    /**
     * 不需要上报的异常
     * @var array
     */
    public $dontReport = [
        BusinessException::class,
    ];

    /**
     * 异常上报
     * @param \Throwable $exception
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
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
        if ($exception instanceof HttpException) {
            return response(json_encode([
                "code" => $exception->getCode(),
                "message" => $exception->getMessage(),
            ], JSON_UNESCAPED_UNICODE), $exception->getStatusCode());
        }
        // 踢出登录
        if ($exception->getCode() === 12000) {
            return $this->kickout($exception->getMessage());
        }
        // 其他异常处理响应
        if (($exception instanceof BusinessException) && ($response = $exception->render($request))) {
            return $response;
        }
        // 返回JSON异常处理
        if ($request->isAjax() || $request->acceptJson()) {
            return $this->fail($exception->getMessage());
        }
        // 返回默认异常处理
        return ExceptionView::render($request, $exception);
    }
}