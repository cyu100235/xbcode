<?php

namespace app\common\service;
use app\common\utils\SettingUtil;

/**
 * 系统信息服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class SystemService
{
    /**
     * 系统信息
     * @var array
     */
    protected static $systemInfo = [
        'version'           => 1000,
        'version_name'      => '1.0.0',
        'name'              => '贵州小白基地网络科技有限公司',
        'logo'              => '',
        'domain'            => 'www.xb-cloud.net',
        'icp'               => '黔ICP备19003265号-1',
        'phone'             => '0851-88888888',
        'email'             => ''
    ];
    
    /**
     * 系统信息
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function info()
    {
        $versionPath = root_path().'info.json';
        if (!file_exists($versionPath)) {
            throw new \Exception('版本文件不存在');
        }
        $systemInfo = json_decode(file_get_contents($versionPath), true);
        // 获取系统配置
        $config = SettingUtil::config('system');
        $systemInfo['web_name'] = $config['web_name'] ?? '';
        $systemInfo['logo'] = $config['web_logo'] ?? '';
        // 合并系统信息
        self::$systemInfo = array_merge(self::$systemInfo, $systemInfo);
        return self::$systemInfo;
    }
}
