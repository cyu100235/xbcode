<?php
namespace app\service\cloud;
use think\facade\Cache;
use yzh52521\EasyHttp\Http;

/**
 * 网络请求服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class HttpCloud
{
    /**
     * 云服务地址
     * @var string
     */
    protected static $baseUrl = 'http://server.dev.xiaobai.host/';

    /**
     * 发送GET请求
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \yzh52521\EasyHttp\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function get(string $url,array $data = [],array $headers = [])
    {
        return self::send($url, 'GET', $data, $headers);
    }

    /**
     * 发送POST请求
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \yzh52521\EasyHttp\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function post(string $url,array $data = [],array $headers = [])
    {
        return self::send($url, 'POST', $data, $headers);
    }

    /**
     * 发送PUT请求
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \yzh52521\EasyHttp\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function put(string $url,array $data = [],array $headers = [])
    {
        return self::send($url, 'PUT', $data, $headers);
    }

    /**
     * 发送DELETE请求
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \yzh52521\EasyHttp\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function delete(string $url,array $data = [],array $headers = [])
    {
        return self::send($url, 'DELETE', $data, $headers);
    }

    /**
     * 发送请求
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return \yzh52521\EasyHttp\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function send(string $url,string $method = 'GET',array $data = [],array $headers = [])
    {
        $url     = self::$baseUrl . $url;
        $headers = array_merge($headers,[
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
        $host = request()->host(true);
        if (Cache::has($host)) {
            $token   = Cache::get($host);
            $headers['Authorization'] = $token;
        }
        $domain = request()->host(true);
        $ip = request()->getRealIp();
        $headers['x-domain'] = $domain;
        $headers['x-ip'] = $ip;
        $http = Http::withHost(self::$baseUrl)->withHeaders($headers);
        $res = [];
        if ($method == 'GET') {
            $res = $http->get($url,$data);
        }
        if ($method == 'POST') {
            $res = $http->post($url,$data);
        }
        if ($method == 'PUT') {
            $res = $http->put($url,$data);
        }
        if ($method == 'DELETE') {
            $res = $http->delete($url,$data);
        }
        return $res;
    }
}