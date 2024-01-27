<?php
namespace app\common\service;

use app\common\service\cloud\AppCloud;
use app\common\service\cloud\AppPluginsCloud;
use app\common\service\cloud\FrameUpdate;
use app\common\service\cloud\ProjectCloud;
use Exception;
use think\facade\Cache;
use think\facade\Cookie;
use yzh52521\EasyHttp\Http;

/**
 * 云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class CloudService
{
    use AppCloud;
    use AppPluginsCloud;
    use ProjectCloud;
    use FrameUpdate;

    /**
     * 请求地址
     * @var string
     */
    protected static $baseUrl = 'http://111.230.55.84:39601/cloud/';

    /**
     * 获取验证码
     * @return \yzh52521\EasyHttp\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function captcha()
    {
        return self::send('Login/captcha');
    }

    /**
     * 用户登录
     * @param string $username
     * @param string $password
     * @param string $scode
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function login(string $username, string $password, string $scode)
    {
        $host = request()->host();
        $params = [
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'scode' => $scode,
        ];
        $data = self::send('Login/login', $params)->array();
        if (empty($data)) {
            throw new Exception('登录失败，接口异常');
        }
        if ($data['code'] === 200) {
            Cache::set($host, $data['data']['token']);
        }
        return $data;
    }

    /**
     * 获取用户信息
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function user()
    {
    }

    /**
     * 发送请求
     * @param string $url
     * @param array $data
     * @param array $options
     * @return \yzh52521\EasyHttp\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function send(string $url, array $data = [], array $options = [])
    {
        $url     = self::$baseUrl . $url;
        $headers = [
            'Accept' => 'application/json',
        ];
        $token   = Cache::get('token');
        if ($token) {
            $headers['Authorization'] = $token;
        }
        $cookies = Cookie::get();
        $domain = request()->rootUrl();
        $res = Http::withHost(self::$baseUrl)
            ->withCookies($cookies,$domain)
            ->withHeaders($headers)
            ->post($url, $data);
        return $res;
    }
}
