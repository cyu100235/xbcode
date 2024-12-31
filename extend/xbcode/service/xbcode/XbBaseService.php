<?php
namespace xbcode\service\xbcode;

use support\Cache;
use yzh52521\EasyHttp\Http;
use Psr\Http\Message\ResponseInterface;

/**
 * 接口基类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbBaseService
{
    /**
     * 接口地址
     * @var string
     */
    private static $url = 'http://server.dev.xbcode.net/api/';

    /**
     * 数据缓存时间(单位:秒)
     * @var int
     */
    protected static $cacheTime = 600;

    /**
     * 用户登录缓存KEY
     * @var string
     */
    protected static $tokenKey = 'xb_server_token';

    /**
     * 获取请求对象
     * @return \yzh52521\EasyHttp\Request
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function request()
    {
        // 获取请求对象
        $server = Http::withHost(self::$url);
        // 设置JSON请求
        $server->asJson();
        // 组装请求头
        $headers = [
            // 设置AJAX请求
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        // 是否携带token
        $token = Cache::get(self::$tokenKey);
        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        }
        $server->withHeaders($headers);
        // 设置响应拦截器
        $server->withResponseMiddleware(function (ResponseInterface $response) {
            // 获取响应数据
            $result = $response->getBody()->getContents();
            $data = json_decode($result, true);
            // 用户登录已过期
            if (isset($data['code']) && $data['code'] == 401) {
                Cache::delete(self::$tokenKey);
            }
            return $response;
        });
        // 返回请求对象
        return $server;
    }
}