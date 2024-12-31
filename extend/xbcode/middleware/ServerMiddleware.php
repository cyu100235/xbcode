<?php
namespace xbcode\middleware;

use Exception;
use support\Cache;
use Webman\Http\Request;
use Webman\Http\Response;
use xbcode\trait\JsonTrait;
use Webman\MiddlewareInterface;
use xbcode\service\xbcode\SiteService;

/**
 * 授权服务中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class ServerMiddleware implements MiddlewareInterface
{
    // 引入JsonTrait
    use JsonTrait;
    
    /**
     * 处理请求
     * @param \Webman\Http\Request $request
     * @param callable $handler
     * @return \Webman\Http\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public function process(Request $request, callable $handler) : Response
    {
        try {
            // 验证是否登录
            $this->loginValidate($request);
        } catch (\Throwable $th) {
            return $this->redirect('/backend/Server/login',$th->getMessage());
        }
        try {
            // 验证是否授权
            $this->authValidate($request);
        } catch (\Throwable $th) {
            return $this->redirect('/backend/Server/authorize',$th->getMessage());
        }
        // 如果是options请求则返回一个空响应，否则继续向洋葱芯穿越，并得到一个响应
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);
        // 返回响应
        return $response;
    }
    
    /**
     * 验证是否登录
     * @param \Webman\Http\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function loginValidate(Request $request)
    {
        // 检测是否插件请求
        if ($request->plugin) {
            return;
        }
        // 获取请求路径
        $path = trim($request->path(), '/');
        // 获取请求信息
        $pathinfo = xbPathInfo($path);
        if (empty($pathinfo)) {
            return;
        }
        // 反射获取控制器属性
        $class      = new \ReflectionClass($pathinfo['class']);
        $props = $class->getDefaultProperties();
        // 拦截无需登录白名单路由
        if (isset($props['server']) && in_array($pathinfo['action'], $props['server'])) {
            return;
        }
        // 验证是否已登录
        $token = Cache::get('xb_server_token');
        if (empty($token)) {
            throw new Exception('');
        }
    }
    
    /**
     * 验证是否授权
     * @param \Webman\Http\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function authValidate(Request $request)
    {
        // 检测是否插件请求
        if ($request->plugin) {
            return;
        }
        // 获取请求路径
        $path = trim($request->path(), '/');
        // 获取请求信息
        $pathinfo = xbPathInfo($path);
        if (empty($pathinfo)) {
            return;
        }
        // 反射获取控制器属性
        $class      = new \ReflectionClass($pathinfo['class']);
        $props = $class->getDefaultProperties();
        // 拦截无需登录白名单路由
        if (isset($props['server']) && in_array($pathinfo['action'], $props['server'])) {
            return;
        }
        if (isset($props['serverAuth']) && in_array($pathinfo['action'], $props['serverAuth'])) {
            return;
        }
        // 获取站点详情
        $data = SiteService::detail();
        // 使用授权过期时间
        $expire = $data['expire_time'] ?? '';
        if (empty($expire)) {
            throw new Exception('');
        }
        // 验证授权是否过期
        $expireTime = strtotime($expire);
        if (time() > $expireTime) {
            throw new Exception('');
        }
    }
}