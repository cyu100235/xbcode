<?php
namespace plugin\xbCode\bootstrap;

use Webman\Bootstrap;
use Workerman\Worker;

/**
 * 框架基础配置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class XbCodeBootstrap implements Bootstrap
{
    /**
     * 入口
     * @param mixed $worker
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function start(?Worker $worker)
    {
        // 设置nginx配置
        static::setNginxConf();
    }

    /**
     * 设置NGINX文件
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    private static function setNginxConf()
    {
        $pluginNginx = base_path() . '/plugin/xbCode/nginx.conf';
        if (!$pluginNginx) {
            return;
        }
        $rootNginx = base_path() . '/nginx.conf';
        if (file_exists($rootNginx)) {
            return;
        }
        copy($pluginNginx, $rootNginx);
    }
}
