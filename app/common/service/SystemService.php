<?php

namespace app\common\service;

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
        'domain'            => 'www.xiaobaijidi.com',
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
        self::$systemInfo = $systemInfo;
        return self::$systemInfo;        
    }
}
