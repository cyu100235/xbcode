<?php

namespace app\common\service\cloud;
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
    public static function getAppList(int $page, int $limit)
    {
        return self::send('Apps/getAppsList', [
            'page'      => $page,
            'limit'     => $limit,
        ]);
    }

    /**
     * 获取应用详情
     * @param string $appName
     * @return mixed
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function getAppInfo(string $appName)
    {
        return self::send('Apps/getAppInfo', [
            'app_name'  => $appName,
        ]);
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
