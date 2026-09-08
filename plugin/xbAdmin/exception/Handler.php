<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\Exception\BusinessException;
use Webman\Exception\ExceptionHandler;
use plugin\xbAdmin\trait\XbJsonTrait;
use plugin\xbAdmin\exception\business\ExceptionBase;

/**
 * 后台异常处理
 *
 * 积木云 SPA 只识别 { msg, status, data, option } 协议，
 * 且 HTTP 状态码恒为 200，登录失效/提示/跳转都要靠 option.eventName 下发，
 * 所以后台必须使用自己的异常处理器，把业务异常翻译成事件协议。
 */
class Handler extends ExceptionHandler
{
    use XbJsonTrait;

    /**
     * 不需要上报的异常
     * @var array
     */
    public $dontReport = [
        ExceptionBase::class,
        BusinessException::class,
    ];

    /**
     * 渲染异常响应
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception): Response
    {
        if ($this->isJson($request)) {
            return $this->renderJson($exception);
        }
        return $this->renderText($exception);
    }

    /**
     * 是否按JSON响应
     * @param Request $request
     * @return bool
     */
    protected function isJson(Request $request): bool
    {
        if ($request->isAjax()) {
            return true;
        }
        $accept = (string) ($request->header('accept') ?? '');
        return str_contains($accept, 'application/json');
    }

    /**
     * 渲染JSON异常
     * @param Throwable $e
     * @return Response
     */
    protected function renderJson(Throwable $e): Response
    {
        $status = (int) ($e->getCode() ?: 500);
        $message = $this->errorMessage($e);
        // 业务异常携带前端事件协议
        if ($e instanceof ExceptionBase) {
            $option = $e->getEventData();
            if (empty($option['eventName'])) {
                $option = $this->notifyData($message);
            }
            $option['state'] = $option['state'] ?? false;
            return $this->json($message, $status, [], $option);
        }
        // 框架异常（404/405等）与参数校验异常统一弹提示
        if ($e instanceof BusinessException) {
            return $this->json($message, $status, [], $this->notifyData($message));
        }
        // 其余异常，调试模式输出定位信息
        $data = [];
        if ($this->debug) {
            $data = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => array_slice(explode("\n", $e->getTraceAsString()), 0, 15),
            ];
        }
        return $this->json($message, $status, $data, $this->notifyData($message));
    }

    /**
     * 渲染文本异常
     * @param Throwable $e
     * @return Response
     */
    protected function renderText(Throwable $e): Response
    {
        $message = $this->errorMessage($e);
        $status = $this->httpStatus($e);
        $content = $this->debug
            ? sprintf('<h1>%d</h1><p>%s</p><pre>%s:%s</pre>', $status, htmlspecialchars($message, ENT_QUOTES), $e->getFile(), $e->getLine())
            : sprintf('<h1>%d</h1><p>%s</p>', $status, htmlspecialchars($message, ENT_QUOTES));
        return new Response($status, ['Content-Type' => 'text/html; charset=utf-8'], $content);
    }

    /**
     * 取异常文案，避免把堆栈细节暴露给用户
     * @param Throwable $e
     * @return string
     */
    protected function errorMessage(Throwable $e): string
    {
        if (
            $e instanceof ExceptionBase
            || $e instanceof BusinessException
        ) {
            return $e->getMessage() !== '' ? $e->getMessage() : '请求处理失败';
        }
        if ($this->debug) {
            return $e->getMessage();
        }
        return '系统繁忙，请稍后再试';
    }

    /**
     * 取HTTP状态码，业务异常统一返回200由前端按status字段判断
     * @param Throwable $e
     * @return int
     */
    protected function httpStatus(Throwable $e): int
    {
        $code = (int) $e->getCode();
        if ($e instanceof ExceptionBase || $e instanceof BusinessException) {
            return 200;
        }
        return $code >= 400 && $code <= 599 ? $code : 500;
    }
}
