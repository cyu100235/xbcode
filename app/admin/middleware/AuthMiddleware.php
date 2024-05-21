<?php
namespace app\admin\middleware;

use app\common\utils\JsonUtil;
use Exception;
use Tinywan\Jwt\JwtToken;
use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

/**
 * 鉴权中间件检测
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AuthMiddleware implements MiddlewareInterface
{
    use JsonUtil;

    /**
     * 处理请求
     * @param \Webman\Http\Request $request
     * @param callable $handler
     * @return \Webman\Http\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function process(Request $request, callable $handler): Response
    {
        try {
            // 鉴权前置钩子
            $this->canAuth($request,$handler);
        } catch (\Throwable $e) {
            return $this->failFul($e->getMessage(), 12000);
        }
        // 鉴权前置钩子
        $response = $request->method() == 'OPTIONS' ? response('', 204) : $handler($request);
        // 鉴权后置钩子
        return $response;
    }

    /**
     * 用户鉴权
     * @param \Webman\Http\Request $request
     * @param callable $handler
     * @throws \Exception
     * @return bool
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function canAuth(Request $request, callable $handler)
    {
        $control = $request->controller;
        // 反射获取控制器属性
        $class = new \ReflectionClass($control);
        $properties = $class->getDefaultProperties();
        // 获取无需登录
        $noLogin = $properties['noLogin'] ?? [];
        // 获取无需鉴权
        $noAuth = $properties['noAuth'] ?? [];
        // 不需要登录
        $action = $request->action;
        if (in_array($action, $noLogin)) {
            return true;
        }
        // 鉴权
        $uid = JwtToken::getCurrentId();
        if (!$uid) {
            throw new Exception('请重新登录');
        }
        // 验证状态
        $state = JwtToken::getExtendVal('state');
        if ($state !== '20') {
            throw new Exception('该用户已被封禁');
        }
        // 不需要鉴权
        if (in_array($action, $noAuth)) {
            return true;
        }
        // 鉴权通过
        return true;
    }
}