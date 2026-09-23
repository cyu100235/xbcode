<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\api;

/**
 * XbCode 框架环境
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class XbCode
{
    /**
     * Env 安装检测
     * @return void
     * @throws \Exception
     */
    public static function checkEnv(): void
    {
        // Env文件检测
        if (!file_exists(self::getEnvPath())) {
            throw new \Exception('请先执行：php webman xb:init 框架初始安装命令');
        }
    }

    /**
     * Env 是否已安装
     * @return bool
     */
    public static function isInstalled(): bool
    {
        return file_exists(self::getEnvPath());
    }

    /**
     * 获取Env文件路径
     * @return string
     */
    public static function getEnvPath(): string
    {
        return base_path('.env');
    }

    /**
     * 数据库连接检测
     * @return void
     * @throws \Exception
     */
    public static function checkDatabase(): void
    {
        try {
            Mysql::query('SELECT 1');
        } catch (\Throwable $e) {
            $message = $e->getMessage();
            // 1045：账号或密码不正确
            if (str_contains($message, '[1045]')) {
                throw new \Exception('数据库账号或密码错误');
            }
            throw new \Exception('数据库连接失败：' . $message);
        }
    }

    /**
     * 数据库连接是否正常
     * @return bool
     */
    public static function isDatabaseConnected(): bool
    {
        try {
            self::checkDatabase();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}