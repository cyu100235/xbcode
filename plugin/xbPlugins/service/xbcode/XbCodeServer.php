<?php
namespace plugin\xbPlugins\service\xbcode;

use support\Cache;
use yzh52521\EasyHttp\Http;
use Psr\Http\Message\ResponseInterface;

/**
 * 接口基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbCodeServer
{
    /**
     * 实例
     * @var XbCodeServer|null
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static $_instance = null;

    /**
     * 获取实例
     * @return XbCodeServer
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function instance()
    {
        if (!static::$_instance) {
            static::$_instance = new static;
        }
        return static::$_instance;
    }

    /**
     * 获取请求对象
     * @return \yzh52521\EasyHttp\Request
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function request()
    {
        // 获取请求地址
        $url = BaseServer::getBaseUrl();
        // 获取请求对象
        $server = Http::withHost($url);
        // 设置JSON请求
        $server->asJson();
        // 组装请求头
        $headers = [
            // 设置AJAX请求
            'X-Requested-With' => 'XMLHttpRequest',
            // 项目标识
            'Xb-Project-Name' => config('project.name', ''),
            // 服务器IP
            'Xb-Server-Ip' => BaseServer::getServiceIp(),
            // 当前域名
            'Xb-Server-Host' => BaseServer::getServiceDomain(),
        ];
        // 是否携带token
        $keyName = BaseServer::getServiceTokenName();
        $token   = Cache::get($keyName);
        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        }
        $server->withHeaders($headers);
        // 设置响应拦截器
        $server->withResponseMiddleware(function (ResponseInterface $response) use ($keyName) {
            // 获取响应数据
            $result = $response->getBody()->getContents();
            $data   = json_decode($result, true);
            // 用户登录已过期
            if (isset($data['code']) && $data['code'] == 12000) {
                Cache::delete($keyName);
            }
            return $response;
        });
        // 返回请求对象
        return $server;
    }
}