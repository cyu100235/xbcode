<?php
/**
 * 积木云渲染器
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\api;

use plugin\xbCode\base\BasePlugin;

/**
 * 插件安装类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Install extends BasePlugin
{
    /**
     * 安装完成之后
     * @param string $version
     * @param array|null $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    protected static function afterInstall(string $version, array|null &$context = null)
    {
        // 删除Channel初始服务配置，沿用插件自带的 channel 进程
        ChannelClient::deleteConfig();
    }
}