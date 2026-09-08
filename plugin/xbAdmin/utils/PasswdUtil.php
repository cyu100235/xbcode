<?php
/**
 * xbAdmin 后台权限管理
 * @package  xbAdmin
 */
namespace plugin\xbAdmin\utils;

/**
 * 密码加密工具
 */
class PasswdUtil
{
    /**
     * 默认加密盐
     * @var string
     */
    protected const DEFAULT_KEY = 'xbadmin';

    /**
     * 生成密码密文
     * @param string $password 明文密码
     * @param string $key 加密盐
     * @return string
     */
    public static function create(string $password, string $key = self::DEFAULT_KEY): string
    {
        return md5($password . md5($password) . md5($key));
    }

    /**
     * 校验明文密码与密文是否一致
     * @param string $password 明文密码
     * @param string $hash 密文密码
     * @param string $key 加密盐
     * @return bool
     */
    public static function check(string $password, string $hash, string $key = self::DEFAULT_KEY): bool
    {
        return hash_equals((string) $hash, static::create($password, $key));
    }
}
