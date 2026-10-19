<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\utils;

/**
 * IP地址工具类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class IpUtil
{
    /**
     * 城市查询结果缓存（进程内复用）
     * @var array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static array $cityCache = [];

    /**
     * ip2region 查询对象（进程内单例）
     * @var mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static $searcher = null;

    /**
     * 获取IP所在城市
     * @param string $ip IP地址
     * @return string 城市名称，如：广东省中山市；内网地址返回“内网”
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function getCity(string $ip): string
    {
        $ip = trim($ip);
        if ($ip === '' || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return '';
        }
        // 内网/回环等特殊地址直接返回，不查询数据库
        if (self::isLocalIp($ip)) {
            return '内网';
        }
        // 命中缓存
        if (array_key_exists($ip, self::$cityCache)) {
            return self::$cityCache[$ip];
        }
        $city = '';
        try {
            $info = self::searcher()->getIpInfo($ip);
            if (is_array($info)) {
                $city = trim(($info['province'] ?? '') . ($info['city'] ?? ''));
            }
        } catch (\Throwable $th) {
            $city = '';
        }
        // 缓存结果
        self::$cityCache[$ip] = $city;
        return $city;
    }

    /**
     * 获取ip2region查询对象
     * @return mixed
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function searcher()
    {
        if (self::$searcher === null) {
            // 使用向量索引缓存策略，减少文件IO，适合常驻进程频繁查询
            self::$searcher = new \Ip2Region('vectorIndex');
        }
        return self::$searcher;
    }

    /**
     * 是否内网/回环/保留地址
     * @param string $ip IP地址
     * @return bool
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function isLocalIp(string $ip): bool
    {
        // 非公网可达地址（含私网、回环、链路本地、保留地址）
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}