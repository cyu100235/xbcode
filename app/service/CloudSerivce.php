<?php
namespace app\service;

use app\service\cloud\LoginCloud;
use app\service\cloud\PluginsOrderCloud;
use app\service\cloud\UtilCloud;
use app\service\cloud\PluginsCateCloud;
use app\service\cloud\PluginsCloud;
use app\service\cloud\PluginsInstallCloud;
use app\service\cloud\PluginsUninstallCloud;
use app\service\cloud\PluginsUpdateCloud;
use app\service\cloud\UserCloud;

/**
 * 云服务
 * @copyright 贵州小白基地网络科技有限公司
 * @author 楚羽幽 cy958416459@qq.com
 */
class CloudSerivce
{
    use UtilCloud;
    use LoginCloud;
    use UserCloud;
    use PluginsCloud;
    use PluginsOrderCloud;
    use PluginsInstallCloud;
    use PluginsUpdateCloud;
    use PluginsUninstallCloud;
    use PluginsCateCloud;
}