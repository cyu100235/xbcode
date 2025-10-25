<?php
/**
 * 积木云渲染器
 *
 * @package  XbCode
 * @author   楚羽幽 <958416459@qq.com>
 * @version  1.0
 * @license  Apache License 2.0
 * @link     http://www.xbcode.net
 * @document http://doc.xbcode.net
 */
namespace plugin\xbCrontab\api;

use plugin\xbCode\api\SettingApi;
use plugin\xbCode\base\BasePlugin;

/**
 * 插件安装类
 * @copyright 贵州积木云网络科技有限公司
 * @author 楚羽幽 958416459@qq.com
 */
class Install extends BasePlugin
{
    /**
     * 安装
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function install(string $version = '', mixed $context = null)
    {
        // 执行安装
        parent::install($version, $context);
        // 可以继续编写你自己安装流程...
    }

    /**
     * 更新
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function update(string $version = '', mixed $context = null)
    {
    }

    /**
     * 卸载
     * @param string $version
     * @param mixed $context
     * @return void
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public static function uninstall(string $version = '', mixed $context = null)
    {
        parent::uninstall($version, $context);
        // 可以继续编写你自己卸载流程...
    }

    /**
     * 获取插件配置
     * @param string $file
     * @return array
     * @copyright 贵州积木云网络科技有限公司
     * @author 楚羽幽 958416459@qq.com
     */
    public function config(string $file)
    {
        $name = static::getCallPluginName();
        return SettingApi::get($name, $file, 'tabs');
    }
}