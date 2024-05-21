<?php
namespace app\common\service;

use app\common\service\cloud\PluginsOrderCloud;
use app\common\service\cloud\PluginsCateCloud;
use app\common\service\cloud\PluginsCloud;
use app\common\service\cloud\UserCloud;

/**
 * 云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class CloudSerivce
{
    use UserCloud;
    use PluginsCloud;
    use PluginsOrderCloud;
    use PluginsCateCloud;
}