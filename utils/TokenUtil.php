<?php
namespace plugin\xbCode\utils;

use Exception;
use Tinywan\Jwt\JwtToken;

/**
 * 令牌工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class TokenUtil
{
    /**
     * 创建令牌
     * @param array $data 扩展数据
     * @param int $expire 过期时间
     * @param string $client 客户端类型
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function create(array $data, int $expire = 7200, string $client = 'web')
    {
        if (!isset($data['id'])) {
            throw new Exception('参数错误，缺少ID');
        }
        if (!isset($data['state'])) {
            throw new Exception('参数错误，缺少状态');
        }
        // 构建数据
        $data  = array_merge($data, [
            // 唯一ID
            'id' => $data['id'],
            // 状态
            'state' => $data['state'],
            // 过期时间
            'access_exp' => $expire,
        ], $data);
        // 转小写
        $client = strtolower($client);
        if ($client === 'mobile') {
            // 移动端
            $data['client'] = JwtToken::TOKEN_CLIENT_MOBILE;
        }else{
            // 网页端
            $data['client'] = JwtToken::TOKEN_CLIENT_WEB;
        }
        $token = JwtToken::generateToken($data);
        return $token;
    }
    
    /**
     * 刷新token
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function refreshToken()
    {
        try {
            // 获取扩展数据
            $data   = JwtToken::getExtend();
            // 创建新令牌
            $result = self::create($data);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage(), 12000);
        }
        return $result;
    }
}
