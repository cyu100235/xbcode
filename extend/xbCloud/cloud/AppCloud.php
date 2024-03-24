<?php

namespace xbCloud\cloud;
use Exception;
use think\facade\Cache;

/**
 * 应用管理服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
trait AppCloud
{
    /**
     * 获取应用列表
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAppList(array $params)
    {
        return self::send('Apps/index', $params)->array();
    }

    /**
     * 获取应用详情
     * @param string $appName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAppDetail(string $appName)
    {
        $params         = [
            'name'      => $appName,
        ];
        return self::send('Apps/detail', $params)->array();
    }

    /**
     * 获取应用安装列表
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAppInstallList()
    {
        $data = self::send('AppsInstall/index')->array();
        if (!isset($data['code']) || !isset($data['data'])) {
            throw new Exception($data['msg'], (int)$data['code']);
        }
        return $data['data'];
    }
    
    /**
     * 获取应用类型
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAppType()
    {
        return self::send('Apps/appType')->array();
    }

    /**
     * 获取应用安装状态
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAppInstall()
    {
        return self::send('Apps/appInstall')->array();
    }

    /**
     * 获取应用分类
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAppCategory()
    {
        return self::send('AppsCate/category')->array();
    }
    
    /**
     * 购买应用
     * @param string $appName
     * @param int $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function buyApp(string $appName, int $version)
    {
        return self::send('Orders/buyApp',[
            'name'      => $appName,
            'version'   => $version,
        ])->array();
    }
    
    /**
     * 下载应用包
     * @param string $appName
     * @param int $version
     * @param string $package
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function downloadApp(string $appName,int $version, string $package)
    {
        $url     = 'Apps/download';
        $devMode = Cache::get('developer_mode','10');
        if ($devMode == '20') {
            $url = 'Developer/download';
        }
        $content = self::send($url,[
            'name'      => $appName,
            'version'   => $version,
        ]);
        $data = $content->array();
        if (is_array($data) && isset($data['code'])) {
            throw new Exception($data['msg'], (int)$data['code']);
        }
        // 检测目录不存在则创建
        $appPath = dirname($package);
        if (!is_dir($appPath)) {
            mkdir($appPath, 0755, true);
        }
        // 保存应用包
        file_put_contents($package, $content);
    }
    
    /**
     * 安装应用
     * @param string $appName
     * @param int $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function installApp(string $appName,int $version)
    {
        return self::send('Apps/install',[
            'name'      => $appName,
            'version'   => $version,
        ])->array();
    }

    /**
     * 更新应用
     * @param string $appName
     * @param int $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function updateApp(string $appName,int $version)
    {
        return self::send('Apps/update',[
            'name'      => $appName,
            'version'   => $version,
        ])->array();
    }

    /**
     * 卸载应用
     * @param string $appName
     * @param int $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function unInstallApp(string $appName,int $version)
    {
        return self::send('Apps/uninstall',[
            'name'      => $appName,
            'version'   => $version,
        ])->array();
    }

    /**
     * 获取应用本地版本号
     * @param string $appName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAppLocalVersion(string $appName)
    {
        $infoPath = root_path("base/{$appName}").'info.json';
        if (!file_exists($infoPath)) {
            throw new Exception('应用信息文件不存在');
        }
        $info = json_decode(file_get_contents($infoPath), true);
        if (!isset($info['version_name'])) {
            throw new Exception('应用版本名称错误');
        }
        if (!isset($info['version'])) {
            throw new Exception('应用版本号错误');
        }
        return $info;
    }
}
