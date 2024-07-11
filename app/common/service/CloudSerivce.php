<?php
namespace app\common\service;

use app\common\service\action\PluginBaseAction;
use app\common\service\action\PluginDependAction;
use app\common\service\cloud\PluginsCloud;
use app\common\service\cloud\PluginsUtil;

/**
 * 云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class CloudSerivce
{
    use PluginsUtil;
    use PluginsCloud;
    use PluginBaseAction;
    use PluginDependAction;
}