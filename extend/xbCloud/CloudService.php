<?php
namespace xbCloud;

use xbCloud\cloud\AppCloud;
use xbCloud\cloud\AppPluginsCloud;
use xbCloud\cloud\DeveloperCloud;
use xbCloud\cloud\FrameUpdate;
use xbCloud\cloud\ProjectCloud;
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
    use DeveloperCloud;

    /**
     * 请求地址
     * @var string
     */
    protected static $baseUrl = 'http://cloud.xiaobai.host/cloud/';

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
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function login(string $username, string $password)
    {
        $host = request()->host(true);
        $params = [
            'host'      => $host,
            'username'  => $username,
            'password'  => $password,
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
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function user()
    {
        $data = self::send('User/index')->array();
        return $data;
    }
    
    /**
     * 在线充值
     * @param float $money
     * @param string $payType
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function recharge(float $money, string $payType)
    {
        return self::send('User/recharge', [
            'money'     => $money,
            'pay_type'  => $payType,
        ])->array();
    }
    
    /**
     * 获取充值二维码
     * @param string $order_no
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getRechargeQrcode(string $order_no,string $payType)
    {
        return self::send('User/getRechargeQrcode', [
            'order_no'  => $order_no,
            'pay_type'  => $payType,
        ]);
    }

    /**
     * 检测充值订单状态
     * @param string $order_no
     * @return \yzh52521\EasyHttp\Response
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function awaitPay(string $order_no)
    {
        $data = self::send('User/awaitPay', [
            'order_no'  => $order_no,
        ])->array();
        return $data;
    }

    /**
     * 获取官方公告
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getNotice()
    {
        $data = self::send('Notice/index')->array();
        return $data;
    }
    
    /**
     * 获取账单信息
     * @param int $page
     * @param int $limit
     * @return array|mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getBill(int $page,int $limit)
    {
        $params = [
            'page'  => $page,
            'limit' => $limit,
        ];
        $data = self::send('Bill/index',$params)->array();
        return $data;
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
        $host = request()->host(true);
        if (Cache::has($host)) {
            $token   = Cache::get($host);
            $headers['Authorization'] = $token;
        }
        $cookies = Cookie::get();
        $domain = request()->host(true);
        $ip = request()->ip();
        $headers['x-domain'] = $domain;
        $headers['x-ip'] = $ip;
        $res = Http::withHost(self::$baseUrl)
            ->withCookies($cookies,$domain)
            ->withHeaders($headers)
            ->post($url, $data);
        return $res;
    }
}
