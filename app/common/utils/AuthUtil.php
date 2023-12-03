<?php

namespace app\common\utils;
use app\common\validate\AuthValidate;
use Exception;
use loong\oauth\facade\Auth;

/**
 * 权限工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class AuthUtil
{
    /**
     * 获取二维菜单数据
     * @param string $plugin
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function canAuth(int $uid,string $path)
    {
    }

    /**
     * 加密用户数据
     * @param array $data
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function encrypt(array $data)
    {
        # 数据验证
        xbValidate(AuthValidate::class,$data);
        $data = [
            'id'            => $data['id'],
            'role_id'       => $data['role_id'],
            'saas_appid'    => $data['saas_appid'] ?? null,
            'pid'           => $data['pid'] ?? null,
            'status'        => $data['status'],
            'is_system'     => $data['is_system'],
        ];
        // 构建令牌（7天有效期）
        $token = Auth::setExpire(3600*24*7)->encrypt($data);
        return $token;
    }

    /**
     * 获取权限规则
     * @param int $uid
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAuthRule(int $uid)
    {
    }
}
