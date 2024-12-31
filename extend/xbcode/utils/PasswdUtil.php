<?php

namespace xbcode\utils;

/**
 * 密码工具类
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class PasswdUtil
{
    /**
     * 创建密码
     * @param string $password 密码
     * @param string $key 密钥
     * @return string
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function create(string $password, string $key = 'xbcode'): string
    {
        $pwdKey = md5($key);
        $pwdMd5 = md5($password);
        $password = md5($password . $pwdMd5 . $pwdKey);
        return $password;
    }
}
