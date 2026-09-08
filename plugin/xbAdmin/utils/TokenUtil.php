<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\utils;

use Exception;
use Throwable;
use Tinywan\Jwt\JwtToken;

/**
 * JWT 令牌工具
 */
class TokenUtil
{
    /**
     * 签发令牌
     * @param array $data 载荷数据，必须包含 id 与 state
     * @param int $expire 过期时间（秒）
     * @param string $client 客户端类型 web|mobile
     * @throws Exception
     * @return array
     */
    public static function create(array $data, int $expire = 86400, string $client = 'web')
    {
        if (!isset($data['id'])) {
            throw new Exception('令牌参数错误：缺少 id 字段');
        }
        if (!isset($data['state'])) {
            throw new Exception('令牌参数错误：缺少 state 字段');
        }
        $data['access_exp'] = $expire;
        $data['client'] = strtolower($client) === 'mobile'
            ? JwtToken::TOKEN_CLIENT_MOBILE
            : JwtToken::TOKEN_CLIENT_WEB;
        return JwtToken::generateToken($data);
    }

    /**
     * 获取当前令牌载荷
     * @throws Throwable
     * @return array
     */
    public static function extend()
    {
        return JwtToken::getExtend();
    }

    /**
     * 刷新令牌
     * @throws Exception
     * @return array
     */
    public static function refreshToken()
    {
        try {
            $data = JwtToken::getExtend();
            $result = static::create($data);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage(), 12000);
        }
        return $result;
    }

    /**
     * 注销当前令牌
     * @param string $client 客户端类型 web|mobile
     * @throws Exception
     * @return bool
     */
    public static function logout(string $client = 'web')
    {
        $jwtClient = strtolower($client) === 'mobile'
            ? JwtToken::TOKEN_CLIENT_MOBILE
            : JwtToken::TOKEN_CLIENT_WEB;
        try {
            return JwtToken::clear($jwtClient);
        } catch (Throwable $e) {
            throw new Exception('退出登录失败：' . $e->getMessage(), 12001);
        }
    }
}
