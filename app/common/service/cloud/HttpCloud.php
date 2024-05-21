<?php
namespace app\common\service\cloud;
use think\facade\Cache;
use yzh52521\EasyHttp\Http;
use yzh52521\EasyHttp\Response;

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
     * 获取数据
     * @param \yzh52521\EasyHttp\Response $result
     * @param bool $isArray
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getContent(Response $result,bool $isArray = true)
    {
        $array = $result->array();
        $body = $result->body();
        if (empty($array) && empty($body)) {
            throw new \Exception('请求失败',500);
        }
        if (is_string($body) && strpos($body,'502 Bad Gateway') !== false) {
            throw new \Exception('远程服务器错误',500);
        }
        if (is_string($body) && strpos($body,'<html>') !== false) {
            throw new \Exception($body,500);
        }
        if (isset($array['code']) && $array['code'] !== 200) {
            throw new \Exception($array['msg'],$array['code']);
        }
        if ($isArray) {
            // 返回数组
            $data = $array['data'] ?? [];
        } else {
            // 返回内容体
            $data = $body;
        }
        return $data;
    }

    /**
     * 获取站点储存token名称
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getTokenName()
    {
        $token   = str_replace('.','_',basename(base_path()));
        return $token;
    }

    /**
     * 获取站点储存token
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getToken()
    {
        $tokenName   = self::getTokenName();
        if (!$tokenName) {
            return '';
        }
        $token = Cache::get($tokenName,'');
        if (!$token) {
            return '';
        }
        if (!isset($token['access_token'])){
            return '';
        }
        if (strpos('Bearer ',$token['access_token']) === false) {
            return 'Bearer ' . $token['access_token'];
        }
        return $token['access_token'];
    }

    /**
     * 设置储存token
     * @param array $data
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function setToken(array $data)
    {
        if (isset($data['expires_in'])) {
            $data['expires_time'] = time() + $data['expires_in'];
        }
        Cache::set(self::getTokenName(),$data);
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
        $referer = request()->header('referer','');
        $host = parse_url($referer,PHP_URL_HOST);
        $headers = array_merge($headers,[
            'Accept'            => 'application/json',
            'Content-Type'      => 'application/x-www-form-urlencoded',
            'X-Requested-With'  => 'XMLHttpRequest',
            'X-Host'            => $host,
        ]);
        // 获取站点储存Token
        $token   = self::getToken();
        if ($token) {
            $headers['Authorization'] = $token;
        }
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