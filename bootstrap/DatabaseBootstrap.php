<?php
namespace plugin\xbCode\bootstrap;

use think\facade\Db;
use function config;
use Webman\Bootstrap;
use Workerman\Worker;

/**
 * 数据库设置
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class DatabaseBootstrap implements Bootstrap
{
    /**
     * 设置数据库
     * @param mixed $worker
     * @return void
     * @copyright 贵州小白基地网络科技有限公司
     * @author 楚羽幽 cy958416459@qq.com
     */
    public static function start(?Worker $worker)
    {
        if (class_exists('Webman\ThinkOrm\ThinkOrm')) {
            $config = config('plugin.xbCode.thinkorm');
            Db::setConfig($config);
        }
    }
}
