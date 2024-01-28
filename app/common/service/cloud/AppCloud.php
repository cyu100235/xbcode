<?php

namespace app\common\service\cloud;
use app\common\model\Apps;
use Exception;

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
        $apps = self::getAppNameVersion();
        $params['apps'] = $apps;
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
        $apps = self::getAppNameVersion();
        $params['apps'] = $apps;
        $params['name'] = $appName;
        return self::send('Apps/detail', $params)->array();
    }

    /**
     * 获取已安装应用名称及版本
     * @return array
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    protected static function getAppNameVersion()
    {
        $field = [
            'name',
            'version_name',
            'version',
        ];
        $list = Apps::order('id desc')->field($field)->select()->toArray();
        $data  = [];
        foreach ($list as $value) {
            $data[$value['name']] = [
                'version_name'  => $value['version_name'],
                'version'       => $value['version'],
            ];
        }
        return $data;
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
     * 创建应用订单
     * @param string $appName
     * @param string $version
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function createAppOrder(string $appName, string $version)
    {
        return self::send('Order/createAppOrder',[
            'app_name'  => $appName,
            'version'   => $version,
        ]);
    }
    
    /**
     * 统应用订单
     * @param string $orderNo
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function UnifiedOrder(string $orderNo)
    {
        return self::send('Order/AppUnifiedOrder',[
            'order_no'  => $orderNo,
        ]);
    }
    
    /**
     * 下载应用包
     * @param string $appName
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function downloadApp(string $appName)
    {
        return self::send('Apps/download',[
            'app_name'  => $appName,
        ]);
    }
}
