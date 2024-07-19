<?php
namespace app\common\exception;

use app\common\utils\JsonUtil;
use Webman\Exception\ExceptionHandler;
use \hg\apidoc\exception\HttpException;
use Webman\Http\Request;
use Webman\Http\Response;
use Throwable;

/**
 * 全局异常处理
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class Handler extends ExceptionHandler
{
    // 引入JsonUtil
    use JsonUtil;

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
        // 其他异常处理响应
        if (($exception instanceof BusinessException) && ($response = $exception->render($request))) {
            return $response;
        }
        // 开启调试模式
        if (config('app.debug')) {
            return parent::render($request, $exception);
        }
        // 返回默认异常处理
        return $this->failFul($exception->getMessage(), 500);
    }

}