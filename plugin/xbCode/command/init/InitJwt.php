<?php
/**
 * 安装JWT密钥
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCode\command\init;

use think\helper\Str;
use Brick\VarExporter\VarExporter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 安装JWT密钥
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class InitJwt
{
    /**
     * 执行安装
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function execute(InputInterface $input, OutputInterface $output): void
    {
        $jwtPath = base_path('config/plugin/tinywan/jwt/app.php');
        if (!file_exists($jwtPath)) {
            $output->writeln("JWT密钥文件不存在...");
            return;
        }
        $jwt = include $jwtPath;
        // 生成JWT密钥
        $jwt['jwt']['access_secret_key'] = self::getJwtKey();
        $jwt['jwt']['refresh_secret_key'] = self::getJwtKey();
        $phpStr = VarExporter::export($jwt);
        $content = <<<PHP
        <?php

        return {$phpStr};
        PHP;
        file_put_contents($jwtPath, $content);
        $output->writeln("安装JWT密钥文件完成...");
    }

    /**
     * 生成JWT密钥
     * @return string
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    private static function getJwtKey(): string
    {
        $year = date('Y');
        $random = Str::random(12);
        $brand = 'xbcode';
        $secretKey = md5(uniqid('xbcode', true));
        return "{$year}{$random}@{$brand}#{$secretKey}";
    }
}
