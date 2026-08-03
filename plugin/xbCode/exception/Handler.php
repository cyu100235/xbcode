<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\exception;

use Exception;
use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;
use support\view\ThinkPHP;
use plugin\xbCode\api\DebugApi;
use plugin\xbCode\trait\JsonTrait;
use Webman\Exception\ExceptionHandler;
use \hg\apidoc\exception\HttpException;
use support\exception\BusinessException;
use plugin\xbCode\exception\business\ExceptionBase;

/**
 * 全局异常处理
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
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
     * @param Throwable $exception
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * 异常处理
     * @param Request $request
     * @param Throwable $e
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function render(Request $request, Throwable $e): Response
    {
        $errCode = $e->getCode() ?: 500;
        $isJson = $request->isAjax() || $request->acceptJson();
        // Apidoc异常处理响应
        if ($e instanceof HttpException) {
            return response(json_encode([
                "code" => $errCode,
                "message" => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE), $errCode);
        }
        // 业务异常处理响应
        if ($e instanceof BusinessException) {
            return $isJson ? $this->renderJson($e) : $this->renderView($e);
        }
        // 业务异常处理
        if ($e instanceof ExceptionBase) {
            // 判断是否为AJAX或JSON请求，非JSON请求，渲染HTML视图
            return $isJson ? $this->renderJson($e) : $this->renderView($e);
        }
        // 业务异常处理
        if ($e instanceof Exception && $isJson) {
            // 返回JSON数据格式
            return $this->renderJson($e);
        }
        // 返回默认异常处理
        return $this->renderView($e);
    }

    /**
     * 渲染JSON
     * @param Throwable $e
     * @return \support\Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function renderJson(Throwable $e)
    {
        $errCode = $e->getCode() ?: 500;
        $eventData = [];
        $debugData = [];
        if (method_exists($e, 'getEventData')) {
            $eventData = $e->getEventData();
        }
        if (DebugApi::status()) {
            $debugData = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
        }
        return $this->json($e->getMessage(), $errCode, $debugData, $eventData);
    }

    /**
     * 渲染异常视图
     * @param Throwable $exception
     * @return Response
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private function renderView(Throwable $exception): Response
    {
        $templatePath = '/plugin/xbCode/exception/view';
        $debug = (bool) config('app.debug', false);
        $file = '';
        $line = '';
        if ($debug) {
            $file = $exception->getFile();
            $line = $exception->getLine();
        }
        $vars = [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'debug' => $debug,
            'file' => $file,
            'line' => $line,
        ];
        return new Response(200, [], ThinkPHP::render($templatePath, $vars));
    }
}