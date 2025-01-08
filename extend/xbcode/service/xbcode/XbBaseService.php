<?php
namespace xbcode\service\xbcode;

use Exception;
use support\Cache;
use app\model\WebSite;
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
     * 生产环境接口地址
     * @var string
     */
    private static $url = 'https://www.xbcode.net/api/';

    /**
     * 开发环境接口地址
     * @var string
     */
    private static $devUrl = 'http://server.dev.xbcode.net/api/';

    /**
     * 数据缓存时间(单位:秒)
     * @var int
     */
    protected static $cacheTime = 600;

    /**
     * 获取服务域名
     * @return array|bool|int|string|null
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getServiceDomain()
    {
        // 当前域名
        $domain = request()->host();
        // 获取租户站点域名
        $webSite = WebSite::getWebSiteDict();
        $webSiteDomain = array_keys($webSite);
        // 检测当前域名是否为租户站点域名
        if (in_array($domain, $webSiteDomain)) {
            // 使用系统配置域名
            $webUrl = config('projects.system_info.system_url', '');
            if (empty($webUrl)) {
                throw new Exception('获取授权域名错误~');
            }
            $domain = parse_url($webUrl, PHP_URL_HOST);
        }
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
            Cache::set($key, $ip, 3600);
        }
        return $ip;
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
     * 获取请求对象
     * @return \yzh52521\EasyHttp\Request
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function request()
    {
        // 获取请求地址
        $url = env('APP_DEBUG_SERVER', false) ? self::$devUrl : self::$url;
        // 获取请求对象
        $server = Http::withHost($url);
        // 设置JSON请求
        $server->asJson();
        // 组装请求头
        $headers = [
            // 设置AJAX请求
            'X-Requested-With' => 'XMLHttpRequest',
        ];
        // 是否携带token
        $keyName = self::getServiceTokenName();
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
            if (isset($data['code']) && $data['code'] == 401) {
                Cache::delete($keyName);
            }
            return $response;
        });
        // 返回请求对象
        return $server;
    }
}