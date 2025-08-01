<?php
namespace plugin\xbPlugins\service\xbcode;

use Exception;
use support\Cache;

/**
 * 基础服务
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class BaseServer
{
    /**
     * 生产环境接口地址
     * @var string
     */
    private static $url = 'https://www.xbcode.net/api/';

    /**
     * 开发环境接口地址
     * @var string
     */
    private static $devUrl = 'http://server.dev.xbcode.net/app/';

    /**
     * 数据缓存时间(单位:秒)
     * @var int
     */
    protected static $cacheTime = 600;

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
     * 获取服务域名
     * @throws \Exception
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getServiceDomain()
    {
        // 当前域名
        $domain = request()->host();
        if (empty($domain)) {
            throw new Exception('获取系统授权域名失败~');
        }
        return $domain;
    }
    
    /**
     * 获取服务IP地址
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getServiceIp()
    {
        $key = "xb_service_ip";
        $ip = Cache::get($key);
        if (empty($ip)) {
            $ip = file_get_contents('http://ifconfig.me/ip');
            Cache::set($key, $ip, 7200);
        }
        return $ip;
    }

    /**
     * 获取请求地址
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getBaseUrl()
    {
        // 是否调试模式
        $debug = getenv('APP_DEBUG') ?: false;
        // 获取请求地址
        $url = $debug ? self::$devUrl : self::$url;
        return $url;
    }

    /**
     * 获取服务缓存KEY
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getServiceTokenName()
    {
        // 服务器IP地址
        $serviceIp = self::getServiceIp();
        // 当前域名
        $domain = self::getServiceDomain();
        // 保存登录信息KEY
        $key = "xb_token_host_{$domain}_ip_{$serviceIp}";
        return $key;
    }

    /**
     * 设置TOKEN
     * @param string $token
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function setToken(string $token)
    {
        $key = self::getServiceTokenName();
        request()->session()->set($key, $token);
    }

    /**
     * 获取TOKEN
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getToken()
    {
        $key = self::getServiceTokenName();
        return request()->session()->get($key);
    }

    /**
     * 清除TOKEN
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function clearToken()
    {
        $key = self::getServiceTokenName();
        request()->session()->delete($key);
    }
}