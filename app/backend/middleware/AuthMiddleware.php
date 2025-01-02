<?php
namespace app\backend\middleware;

use Exception;
use app\model\AdminRole;
use app\model\AdminRule;
use Webman\Http\Request;
use Tinywan\Jwt\JwtToken;
use Webman\Http\Response;
use xbcode\trait\JsonTrait;
use xbcode\utils\TokenUtil;
use Webman\MiddlewareInterface;
use xbcode\providers\QueueProvider;

/**
 * 权限中间件
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AuthMiddleware implements MiddlewareInterface
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
    public function process(Request $request, callable $handler): Response
    {
        /**
         * 如果是options请求则返回一个空响应，否则继续向洋葱芯穿越，并得到一个响应
         * @var Response
         */
        $response = $request->method() == 'OPTIONS' ? response('') : $handler($request);

        try {
            // 权限检测
            $this->validateAuth($request);
        } catch (\Throwable $th) {
            // 没有操作权限
            if ($th->getCode() === 403) {
                return $this->redirect('/', $th->getMessage(), 'error');
            }
            // 刷新令牌
            if ($th->getCode() === 600) {
                // 刷新令牌
                $result = TokenUtil::refreshToken();
                // 设置刷新令牌
                $response->withHeader('refresh-token', $result['access_token']);
                $response->withHeader('refresh-type', $result['token_type'] ?? '');
                // 设置响应状态码
                $response->withStatus(200);
                // 返回响应
                return $response;
            }
            // 抛出异常
            throw new Exception($th->getMessage(), $th->getCode());
        }

        // 记录日志
        $this->addLog($request, $response);

        // 返回响应
        return $response;
    }

    /**
     * 权限检测
     * @param \Webman\Http\Request $request
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function validateAuth(Request $request)
    {
        // 获取请求路径信息
        $pathInfo = xbPathInfo($request->path());
        if (empty($pathInfo)) {
            return;
        }
        // 反射获取控制器属性
        $class = new \ReflectionClass($pathInfo['class']);
        $props = $class->getDefaultProperties();
        // 不需要登录的接口
        $noLogin = $props['noLogin'] ?? [];
        if (in_array($pathInfo['action'], $noLogin)) {
            return;
        }
        try {
            // 获取管理员ID
            $adminId = JwtToken::getCurrentId();
            // 获取管理员状态
            $adminState = JwtToken::getExtendVal('state');
            // 是否系统管理员
            $isAdmin = JwtToken::getExtendVal('is_system');
            // 获取令牌剩余有效期
            $expireSecond = JwtToken::getTokenExp();
            // 设置请求管理员ID
            $request->uid = $adminId;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 12000);
        }
        // 令牌过一半时间则刷新
        $expireTime = (int) ($expireSecond / 2);
        if ($expireTime <= 0) {
            throw new Exception('刷新令牌', 600);
        }
        if ($adminState === '10') {
            throw new Exception('账号已被禁用', 404);
        }
        // 系统管理员不验证权限
        if ($isAdmin === '20') {
            return;
        }
        // 不需要验证权限的接口
        $noAuth = $props['noAuth'] ?? [];
        if (in_array($pathInfo['action'], $noAuth)) {
            return;
        }
        // 检测是否有操作权限
        if (!AdminRole::checkAuth($adminId, $pathInfo['path'])) {
            throw new Exception('您没有操作权限', 403);
        }
    }

    /**
     * 记录日志
     * @param \Webman\Http\Request $request
     * @param \Webman\Http\Response $response
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private function addLog(Request $request, Response $response)
    {
        // 请求类型
        $method = $request->method();
        // 记录日志
        if ($method !== 'GET' && $request->uid) {
            // 管理员ID
            $adminId = (int) $request->uid;
            // 管理员账号
            $adminName = JwtToken::getExtendVal('username');
            // 请求IP
            $realIp = $request->getRealIp(true);
            // 请求路径
            $path = trim($request->path(), '/');
            $path = ltrim($path, '/');
            // 获取菜单字典
            $menus = AdminRule::getMenuDict();
            // 菜单名称
            $title = $menus[$path]['title'] ?? '未知菜单';
            // 请求参数
            $query = $request->post();
            $query = is_array($query) ? json_encode($query, 256) : $query;
            // 响应结果
            $result = $response->rawBody();
            $result = empty($result) ? '' : $result;
            $result = is_array($result) ? json_encode($result) : $result;
            // 添加日志至队列
            $taskData = [
                'type' => '10',
                'admin_id' => $adminId,
                'admin_name' => $adminName,
                'real_ip' => $realIp,
                'path' => $path,
                'method' => $method,
                'title' => $title,
                'query' => $query,
                'result' => $result,
            ];
            QueueProvider::addAsync('backend_log', $taskData, '', 10);
        }
    }
}