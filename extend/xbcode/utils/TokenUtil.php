<?php
namespace xbcode\utils;

use Exception;
use Tinywan\Jwt\JwtToken;

/**
 * 权限工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class TokenUtil
{
    /**
     * 创建token
     * @param array $data
     * @param mixed $expire
     * @param string $client
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function create(array $data, $expire = 604800, string $client = 'WEB')
    {
        if (!isset($data['id'])) {
            throw new Exception('参数错误，缺少ID');
        }
        if (!isset($data['state'])) {
            throw new Exception('参数错误，缺少状态');
        }
        // 构建数据
        $data  = array_merge($data, [
            'id' => $data['id'],
            'state' => $data['state'],
            'client' => $client,
            'access_exp' => $expire,
        ], $data);
        $token = JwtToken::generateToken($data);
        return $token;
    }

    /**
     * 刷新token
     * 用于单点登录的刷新token，无单点登录可不实现
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function refreshToken()
    {
    }
}
